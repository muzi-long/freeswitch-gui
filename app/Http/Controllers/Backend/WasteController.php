<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class WasteController extends Controller
{

    /**
     * 公海库列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all(['merchant_id','company_name','contact_name','contact_phone']);
            $res = Project::query()
                ->where('owner_user_id',-1)
                //联系人
                ->when($data['contact_name'], function ($query) use ($data) {
                    return $query->where('contact_name', $data['contact_name']);
                })
                //联系电话
                ->when($data['contact_phone'], function ($query) use ($data) {
                    return $query->where('contact_phone', $data['contact_phone']);
                })
                //公司名称
                ->when($data['company_name'], function ($query) use ($data) {
                    return $query->where('company_name', '%' . $data['company_name'] . '%');
                })
                //商户
                ->when($data['merchant_id'], function ($query) use ($data) {
                    return $query->where('merchant_id', $data['merchant_id']);
                })
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('backend.crm.waste.index');
    }


    /**
     * 项目详情
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $model = Project::with('designs')->findOrFail($id);
        return View::make('backend.crm.waste.show',compact('model'));
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        DB::beginTransaction();
        try {
            DB::table('project')->whereIn('id',$ids)->delete();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除公海库记录异常：'.$exception->getMessage(),$ids);
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }

    }


}
