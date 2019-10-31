<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BillRequest;
use App\Models\Bill;
use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchants = Merchant::orderByDesc('id')->get();
        return view('admin.bill.index',compact('merchants'));
    }

    public function data(Request $request)
    {
        $merchant_id = $request->post('merchant_id');
        $type = $request->post('type');
        $created_at_start = $request->post('created_at_start');
        $created_at_end = $request->post('created_at_end');
        $res = Bill::with('merchant')->orderBy('id','desc')
            ->when($merchant_id,function ($query) use ($merchant_id){
                return $query->where('merchant_id',$merchant_id);
            })
            ->when($type,function ($query) use ($type){
                return $query->where('type',$type);
            })
            ->when($created_at_start&&!$created_at_end,function ($query) use ($created_at_start){
                return $query->where('created_at','>=',$created_at_start);
            })
            ->when(!$created_at_start&&$created_at_end,function ($query) use ($created_at_end){
                return $query->where('created_at','<=',$created_at_end);
            })
            ->when($created_at_start&&$created_at_end,function ($query) use ($created_at_start,$created_at_end){
                return $query->whereBetween('created_at',[$created_at_start,$created_at_end]);
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
        $data = $request->all(['merchant_id','type','money','remark']);
        $merchant = Merchant::where('id',$data['merchant_id'])->first();
        if (!$merchant){
            return response()->json(['code'=>1, 'msg'=>'商户不存在']);
        }
        $data = array_prepend($data, $request->user()->id, 'created_user_id');

        //金额转换
        $data['money'] = $data['type']==1?abs($data['money']):-1*abs($data['money']);
        //开启事务
        DB::beginTransaction();
        try{
            DB::table('bill')->insert(array_merge($data,['created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]));
            DB::table('merchant')->where('id',$data['merchant_id'])->increment('money',$data['money']);
            DB::commit();
            return response()->json(['code'=>0,'msg'=>'操作成功']);
        }catch (\Exception $exception){
            DB::rollback();
            return response()->json(['code'=>1,'msg'=>'操作失败：'.$exception->getMessage()]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
