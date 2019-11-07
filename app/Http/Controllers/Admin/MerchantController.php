<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Models\Gateway;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\Sip;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $res = Merchant::orderBy('id','desc')
            ->when($data['username'],function ($query) use ($data){
                return $query->where('username','like','%'.$data['username'].'%');
            })
            ->when($data['company_name'],function ($query) use ($data){
                return $query->where('company_name','like','%'.$data['company_name'].'%');
            })
            ->when($data['status']!==null,function ($query) use ($data){
                return $query->where('status',$data['status']);
            })
            ->when($data['expires_at_start']&&!$data['expires_at_end'],function ($query) use ($data){
                return $query->where('expires_at','>=',$data['expires_at_start']);
            })
            ->when(!$data['expires_at_start']&&$data['expires_at_end'],function ($query) use ($data){
                return $query->where('expires_at','<=',$data['expires_at_end']);
            })
            ->when($data['expires_at_start']&&$data['expires_at_end'],function ($query) use ($data){
                return $query->whereBetween('expires_at',[$data['expires_at_start'],$data['expires_at_end']]);
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
            'company_name',
            'contact_name',
            'contact_phone',
            'status',
            'expires_at',
            'sip_num',
            'member_num',
            'queue_num',
        ]);
        $data['uuid'] = Uuid::uuid();
        try{
            Merchant::create($data);
            return redirect()->to(route('admin.merchant'))->with(['success'=>'添加成功']);
        }catch (\Exception $e){
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $merchant = Merchant::with(['gateways'])->findOrFail($id);
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
        $members = Member::where('merchant_id',$merchant->id)->orderByDesc('id')->get();
        //分机
        $sips = Sip::where('merchant_id',$merchant->id)->orderByDesc('id')->get();
        //角色
        $roles = Role::where('guard_name','merchant')->get();
        return View::make('admin.merchant.show',compact('merchant','gateways','members','sips','roles'));
    }

    public function assignRole(Request $request,$id)
    {
        $user = Merchant::findOrFail($id);
        $roles = $request->get('roles',[]);
        try{
            $user->syncRoles($roles);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
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
            'company_name',
            'contact_name',
            'contact_phone',
            'status',
            'expires_at',
            'sip_num',
            'member_num',
            'queue_num',
        ]);
        if ($data['password']==null){
            unset($data['password']);
        }
        try{
            $model->update($data);
            return redirect()->to(route('admin.merchant'))->with(['success'=>'更新成功']);
        }catch (\Exception $e){
            return back()->withErrors($e->getMessage());
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

    /**
     * 帐单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill(Request $request)
    {
        $merchant_id = $request->get('merchant_id');
        $merchant = Merchant::findOrFail($merchant_id);
        return view('admin.merchant.bill',compact('merchant'));
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

}
