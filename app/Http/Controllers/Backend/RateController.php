<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Call\Rate\StoreRequest;
use App\Http\Requests\Backend\Call\Rate\UpdateRequest;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RateController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Rate::query()
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        return View::make('backend.call.rate.index');
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('backend.call.rate.create');
    }

    /**
     * 添加
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'name',
            'description',
            'cost',
            'time',
        ]);
        $data['cost'] *= 100;
        try {
            Rate::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.rate')]);
        }catch (\Exception $exception){
            Log::error('添加费率异常：'.$exception->getMessage(),$data);
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
        $model = Rate::find($id);
        return View::make('backend.call.rate.edit',compact('model'));
    }

    /**
     * 更新
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request,$id)
    {
        $model = Rate::findOrFail($id);
        $data = $request->all([
            'name',
            'description',
            'cost',
            'time',
        ]);
        $data['cost'] *= 100;
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.rate')]);
        }catch (\Exception $exception){
            Log::error('更新费率异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            Rate::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除费率异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
