<?php

namespace App\Http\Controllers\Admin;

use App\Models\Digits;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DigitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ivr_id = $request->get('ivr_id');
        return view('admin.digits.index',compact('ivr_id'));
    }

    public function data(Request $request)
    {
        $ivr_id = $request->get('ivr_id');
        $res = Digits::when($ivr_id,function ($query) use ($ivr_id){
            $query->where('ivr_id',$ivr_id);
        })->orderByDesc('id')->paginate($request->get('limit', 30));
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->get('parm');
        try{
            foreach ($data as $d){
                Digits::create($d);
            }
            return response()->json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'添加失败']);
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
