<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SipListRequest;
use App\Http\Requests\SipRequest;
use App\Models\Merchant;
use App\Models\Sip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class SipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.sip.index');
    }

    public function data(Request $request)
    {
        $query = Sip::query();
        $username = $request->get('username');
        if ($username){
            $query = $query->where('username',$username);
        }
        $res = $query->orderByDesc('id')->paginate($request->get('limit', 30));
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
        $merchants = Merchant::orderByDesc('id')->where('status',1)->get();

        return view('admin.sip.create',compact('merchants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SipRequest $request)
    {
        $data = $request->all([
            'merchant_gateway',
            'gateway_id',
            'expense_id',
            'merchant_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        $mg = explode(',',$data['merchant_gateway']);
        $data['merchant_id'] = $mg[0];
        $data['gateway_id'] = $mg[1];
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        //验证商户允许的最大分机数
        $merchant = Merchant::withCount('sips')->findOrFail($data['merchant_id']);
        if ($merchant->sips_count >= $merchant->sip_num){
            return back()->withInput()->withErrors(['error'=>'添加失败：超出商户最大允许分机数量']);
        }
        try{
            Sip::create($data);
            return redirect(route('admin.sip'))->with(['success'=>'添加成功']);
        }catch (\Exception $exception){
            return back()->withInput()->withErrors(['error'=>'添加失败：'.$exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Sip::findOrFail($id);
        $merchants = Merchant::orderByDesc('id')->where('status',1)->get();
        return view('admin.sip.edit',compact('model','merchants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SipRequest $request, $id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'merchant_gateway',
            'gateway_id',
            'expense_id',
            'merchant_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        $mg = explode(',',$data['merchant_gateway']);
        $data['merchant_id'] = $mg[0];
        $data['gateway_id'] = $mg[1];
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        try{
            $model->update($data);
            return redirect(route('admin.sip'))->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            return back()->withErrors(['error'=>'更新失败：'.$exception->getMessage()]);
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
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Sip::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function createList()
    {
        $merchants = Merchant::orderByDesc('id')->where('status',1)->get();
        return view('admin.sip.create_list',compact('merchants'));
    }

    public function storeList(SipListRequest $request)
    {
        $data = $request->all(['sip_start','sip_end','password','merchant_gateway']);
        $mg = explode(',',$data['merchant_gateway']);
        $data['merchant_id'] = $mg[0];
        $data['gateway_id'] = $mg[1];
        if ($data['sip_start'] <= $data['sip_end']){
            //验证商户允许的最大分机数
            $merchant = Merchant::withCount('sips')->findOrFail($data['merchant_id']);
            if (($merchant->sips_count+($data['sip_end']-$data['sip_start']+1)) >= $merchant->sip_num){
                return back()->withInput()->withErrors(['error'=>'添加失败：超出商户最大允许分机数量'.$merchant->sips_count.'<=>'.$merchant->sip_num]);
            }
            //开启事务
            DB::beginTransaction();
            try{
                for ($i=$data['sip_start'];$i<=$data['sip_end'];$i++){
                    DB::table('sip')->insert([
                        'merchant_id' => $data['merchant_id'],
                        'gateway_id' => $data['gateway_id'],
                        'username'  => $i,
                        'password'  => $data['password'],
                        'effective_caller_id_name' => $i,
                        'effective_caller_id_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                DB::commit();
                return redirect(route('admin.sip'))->with(['success'=>'添加成功']);
            }catch (\Exception $e) {
                DB::rollback();
                return back()->withInput()->withErrors(['error'=>'添加失败：'.$e->getMessage()]);
            }
        }
        return back()->withInput()->withErrors(['error'=>'开始分机号必须小于等于结束分机号']);
    }
    
}
