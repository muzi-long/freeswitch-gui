<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SipListRequest;
use App\Http\Requests\SipRequest;
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
        return view('admin.sip.create');
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
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        if (Sip::create($data)){
            return redirect(route('admin.sip'))->with(['success'=>'添加成功']);
        }
        return back()->withErrors(['error'=>'添加失败']);

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
        return view('admin.sip.edit',compact('model'));
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
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        if ($model->update($data)){
            return redirect(route('admin.sip'))->with(['success'=>'更新成功']);
        }
        return back()->withErrors(['error'=>'更新失败']);
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
        return view('admin.sip.create_list');
    }

    public function storeList(SipListRequest $request)
    {
        $data = $request->all(['sip_start','sip_end','password']);
        if ($data['sip_start'] <= $data['sip_end']){
            //开启事务
            DB::beginTransaction();
            try{
                for ($i=$data['sip_start'];$i<=$data['sip_end'];$i++){
                    DB::table('sip')->insert([
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
                return back()->withInput()->withErrors(['error'=>'添加失败']);
            }
        }
        return back()->withInput()->withErrors(['error'=>'开始分机号必须小于等于结束分机号']);
    }
    
}
