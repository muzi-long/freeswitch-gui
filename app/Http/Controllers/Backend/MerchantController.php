<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Platform\Merchant\StoreRequest;
use App\Http\Requests\Backend\Platform\Merchant\UpdateRequest;
use App\Models\Freeswitch;
use App\Models\Merchant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class MerchantController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all(['freeswitch_id','company_name','contact_name','contact_phone']);
            $res = Merchant::query()
            ->when($data['freeswitch_id'],function ($q) use ($data){
                return $q->where('freeswitch_id',$data['freeswitch_id']);
            })
            ->when($data['company_name'],function ($q) use ($data){
                return $q->where('company_name','like','%'.$data['company_name'].'%');
            })
            ->when($data['contact_name'],function ($q) use ($data){
                return $q->where('contact_name',$data['contact_name']);
            })
            ->when($data['contact_phone'],function ($q) use ($data){
                return $q->where('contact_phone',$data['contact_phone']);
            })
            ->orderBy('id','desc')
            ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        $fs = Freeswitch::orderBy('id','desc')->get();
        return View::make('backend.platform.merchant.index',compact('fs'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fs = Freeswitch::orderBy('id','desc')->get();
        return View::make('backend.platform.merchant.create',compact('fs'));
    }

    /**
     * 添加
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'username',
            'password',
            'nickname',
            'company_name',
            'contact_name',
            'contact_phone',
            'staff_num',
            'sip_num',
            'gateway_num',
            'agent_num',
            'queue_num',
            'task_num',
            'expire_at',
            'freeswitch_id',
        ]);
        DB::beginTransaction();
        try {
            $merchant_id = DB::table('merchant')->insertGetId([
                'company_name' => $data['company_name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'staff_num' => $data['staff_num'],
                'sip_num' => $data['sip_num'],
                'gateway_num' => $data['gateway_num'],
                'agent_num' => $data['agent_num'],
                'queue_num' => $data['queue_num'],
                'task_num' => $data['task_num'],
                'expire_at' => $data['expire_at'],
                'created_at' => date('Y-m-d H:i:s'),
                'freeswitch_id' => $data['freeswitch_id'],
            ]);
            DB::table('staff')->insert([
                'username' => $data['username'],
                'password' => bcrypt($data['password']),
                'nickname' => $data['nickname'],
                'merchant_id' => $merchant_id,
                'is_merchant' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.platform.merchant')]);
        }catch (\Exception $exception){
            Log::error('添加商户异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Merchant::findOrFail($id);
        $staff = Staff::where('merchant_id',$id)->where('is_merchant',1)->first();
        $fs = Freeswitch::orderBy('id','desc')->get();
        return View::make('backend.platform.merchant.edit',compact('model','staff','fs'));
    }

    /**
     * 更新
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request,$id)
    {
        $merchant = Merchant::findOrFail($id);
        $data = $request->all([
            //'username',
            //'password',
            'nickname',
            'company_name',
            'contact_name',
            'contact_phone',
            'staff_num',
            'sip_num',
            'gateway_num',
            'agent_num',
            'queue_num',
            'task_num',
            'expire_at',
            'freeswitch_id',
        ]);
        DB::beginTransaction();
        try {
            DB::table('merchant')->where('id',$merchant->id)->update([
                'company_name' => $data['company_name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'staff_num' => $data['staff_num'],
                'sip_num' => $data['sip_num'],
                'gateway_num' => $data['gateway_num'],
                'agent_num' => $data['agent_num'],
                'queue_num' => $data['queue_num'],
                'task_num' => $data['task_num'],
                'expire_at' => $data['expire_at'],
                'updated_at' => date('Y-m-d H:i:s'),
                'freeswitch_id' => $data['freeswitch_id'],
            ]);
            DB::table('staff')
                ->where('merchant_id',$merchant->id)
                ->where('is_merchant',1)
                ->update([
                    'nickname' => $data['nickname'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.platform.merchant')]);
        }catch (\Exception $exception){
            Log::error('更新商户异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            Merchant::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除商户异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 帐单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill($id)
    {
        $merchant = Merchant::find($id);
        return view('backend.platform.merchant.bill', compact('merchant'));
    }

}
