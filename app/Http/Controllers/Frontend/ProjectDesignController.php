<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\ProjectDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ProjectDesignController extends Controller
{

    /**
     * 项目表单设计列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $res = ProjectDesign::query()
                ->where('merchant_id',$request->user()->merchant_id)
                ->orderBy('sort', 'asc')
                ->orderBy('id', 'desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('frontend.crm.project_design.index');
    }


    /**
     * 添加字段
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('frontend.crm.project_design.create');
    }

    /**
     * 添加字段
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all([
            'field_label',
            'field_key',
            'field_type',
            'field_option',
            'field_value',
            'field_tips',
            'sort',
            'visiable',
            'required'
        ]);
        $data['merchant_id'] = $request->user()->merchant_id;
        //验证field_key是否重复
        $hasExisit = ProjectDesign::where('field_key', $data['field_key'])
            ->where('merchant_id', $data['merchant_id'])
            ->count();
        if (in_array($data['field_key'], config('freeswitch.project_design_default_field')) || $hasExisit) {
            return Response::json(['code' => 1, 'msg' => '字段Key已存在']);
        }
        try {
            if ($data['visiable'] == null) {
                $data['visiable'] = 2;
            }
            if ($data['required'] == null) {
                $data['required'] = 2;
            }
            ProjectDesign::create($data);
            return Response::json(['code' => 0, 'msg' => '添加成功', 'url' => route('frontend.crm.project-design')]);
        } catch (\Exception $exception) {
            Log::error('添加表单设计异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '添加失败']);
        }
    }

    /**
     * 更新表单设计
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id)
    {
        $model = ProjectDesign::where('merchant_id',$request->user()->merchant_id)->findOrFail($id);
        return View::make('frontend.crm.project_design.edit', compact('model'));
    }

    /**
     * 更新表单设计
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $model = ProjectDesign::findOrFail($id);
        $data = $request->all([
            'field_label',
            'field_key',
            'field_type',
            'field_option',
            'field_value',
            'field_tips',
            'sort',
            'visiable',
            'required'
        ]);
        //验证field_key是否重复
        $hasExisit = ProjectDesign::where('field_key', $data['field_key'])
            ->where('id', '!=', $id)
            ->where('merchant_id', $model->merchant_id)
            ->count();
        if (in_array($data['field_key'], config('freeswitch.project_design_default_field')) || $hasExisit) {
            return Response::json(['code' => 1, 'msg' => '字段Key已存在']);
        }
        if ($data['visiable'] == null) {
            $data['visiable'] = 2;
        }
        if ($data['required'] == null) {
            $data['required'] = 2;
        }
        try {
            $model->update($data);
            return Response::json(['code' => 0, 'msg' => '更新成功', 'url' => route('frontend.crm.project-design')]);
        } catch (\Exception $exception) {
            Log::error('更新表单设计异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '更新失败']);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        //删除
        DB::beginTransaction();
        try {
            DB::table('project_design')
                ->where('merchant_id',$request->user()->merchant_id)
                ->whereIn('id', $ids)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            DB::table('project_design_value')
                ->whereIn('project_design_id', $ids)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            DB::commit();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('删除表单字段异常：' . $exception->getMessage(), $ids);
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }

}
