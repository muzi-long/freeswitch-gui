<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Models\Gateway;
use App\Models\Merchant;
use App\Models\MerchantInfo;
use App\Models\Role;
use App\Models\Sip;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.merchant.index');
    }

    public function data(Request $request)
    {
        $data = $request->all(['username','company_name','status','expires_at_start','expires_at_end']);
        $res = Merchant::with('info')
            ->where('merchant_id',0)
            ->orderBy('id','desc')
            ->when($data['username'],function ($query) use ($data){
                return $query->where('username','like','%'.$data['username'].'%');
            })
            ->when($data['status']!==null,function ($query) use ($data){
                return $query->where('status',$data['status']);
            })
            ->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.merchant.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MerchantCreateRequest $request)
    {
        $data = $request->all([
            'username',
            'password',
            'contact_name',
            'contact_phone',
            'status',
        ]);
        $dataInfo = $request->all([
            'company_name',
            'expires_at',
            'sip_num',
            'member_num',
            'queue_num',
        ]);
        $data['uuid'] = Uuid::uuid();
        try{
            $data['password'] = bcrypt($data['password']);
            $merchant = Merchant::create($data);
            if ($merchant){
                $dataInfo['merchant_id'] = $merchant->id;
                MerchantInfo::create($dataInfo);
            }
            return redirect()->to(route('admin.merchant'))->with(['success'=>'添加成功']);
        }catch (\Exception $e){
            Log::info('添加商户异常：'.$e->getMessage());
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $merchant = Merchant::with(['info','gateways'])->findOrFail($id);
        $roles = Role::where('guard_name','merchant')->get();
        foreach ($roles as $role){
            $role->own = $merchant->hasRole($role) ? true : false;
        }
        $gateways = Gateway::get();
        foreach ($gateways as $gateway){
            if ($merchant->gateways->isNotEmpty()){
                foreach ($merchant->gateways as $g1){
                    if ($g1->id == $gateway->id){
                        $gateway->rate = $g1->pivot->rate;
                    }
                }
            }
        }
        //子帐号
        $accounts = Merchant::where('merchant_id',$merchant->id)->orderByDesc('id')->get();
        //分机
        $sips = Sip::where('merchant_id',$merchant->id)->orderByDesc('id')->get();

        return View::make('admin.merchant.show',compact('merchant','gateways','accounts','sips','roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Merchant::findOrFail($id);
        return view('admin.merchant.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MerchantUpdateRequest $request, $id)
    {
        $model = Merchant::findOrFail($id);
        $data = $request->all([
            'username',
            'password',
            'contact_name',
            'contact_phone',
            'status',
        ]);
        $dataInfo = $request->all([
            'company_name',
            'expires_at',
            'sip_num',
            'member_num',
            'queue_num',
        ]);

        DB::beginTransaction();
        try{
            if ($data['password']==null){
                unset($data['password']);
            }else{
                $data['password'] = bcrypt($data['password']);
            }
            DB::table('merchant')->where('id',$id)->update($data);
            DB::table('merchant_info')->where('merchant_id',$id)->update($dataInfo);
            DB::commit();
            return redirect()->to(route('admin.merchant'))->with(['success'=>'更新成功']);
        }catch (\Exception $e){
            DB::rollBack();
            Log::info('更新商户异常：'.$e->getMessage());
            return back()->withErrors('更新失败');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        //验证参数
        if (!is_array($ids)||empty($ids)){
            return response()->json(['code'=>1, 'msg'=>'请选择删除项']);
        }
        //删除
        try{
            Merchant::whereIn('id',$ids)->delete();
            return response()->json(['code'=>0, 'msg'=>'删除成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1, 'msg'=>$exception->getMessage()]);
        }
    }

    public function gateway($id)
    {
        $merchant = Merchant::with('gateways')->findOrFail($id);
        $gateways = Gateway::get();
        foreach ($gateways as $gateway){
            if ($merchant->gateways->isNotEmpty()){
                foreach ($merchant->gateways as $g1){
                    if ($g1->id == $gateway->id){
                        $gateway->rate = $g1->pivot->rate;
                    }
                }
            }
        }
        return view('admin.merchant.gateway',compact('merchant','gateways'));
    }

    public function assignGateway(Request $request, $id)
    {
        $merchant = Merchant::with('gateways')->findOrFail($id);
        $gateway_ids = $request->get('gateways',[]);
        try{
            $sync_data = [];
            foreach ($gateway_ids as $v){
                if (isset($v['id']) && is_numeric($v['rate'])){
                    $sync_data[$v['id']] = ['rate'=>$v['rate']];
                }
            }
            $merchant->gateways()->sync($sync_data);
            return redirect()->to(route('admin.merchant'))->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            return back()->withErrors('更新失败');
        }
    }

    /**
     * 授予角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request,$id)
    {
        $merchant = Merchant::findOrFail($id);
        $roles = $request->get('roles',[]);
        try{
            $merchant->syncRoles($roles);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

}
