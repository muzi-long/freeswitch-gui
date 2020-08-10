<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Imports\ProjectImport;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{

    /**
     * 待分配列表
     * @param Request $request
     * @return mixed
     */
    public function assignment(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all([
                'contact_name',
                'contact_phone',
                'company_name',
            ]);
            $res = Project::with(['node','followUser'])
                //姓名
                ->when($data['contact_name'],function ($query) use($data){
                    return $query->where('contact_name',$data['contact_name']);
                })
                //联系电话
                ->when($data['contact_phone'],function ($query) use($data){
                    return $query->where('contact_phone',$data['contact_phone']);
                })
                //公司名称
                ->when($data['company_name'], function ($query) use ($data) {
                    return $query->where('company_name', '%' . $data['company_name'] . '%');
                })
                ->where('owner_user_id','=',0)
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        $staffs = Staff::where('merchant_id',$request->user()->merchant_id)->where('is_merchant',0)->get();
        return View::make('frontend.crm.project.assignment',compact('staffs'));
    }

    /**
     * 分配
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignmentTo(Request $request)
    {
        $data = $request->all(['project_ids','staff_id']);
        //验证
        if (!is_array($data['project_ids'])){
            return Response::json(['code'=>1,'msg'=>'参数错误']);
        }
        //验证project_ids是否属于商户
        $res = Project::whereIn('id',$data['project_ids'])->where('merchant_id',$request->user()->merchant_id)->count();
        if ($res != count($data['project_ids'])){
            return Response::json(['code'=>1,'msg'=>'请选择本商户的客户']);
        }
        //验证接收人
        $staff = Staff::where('merchant_id',$request->user()->merchant_id)->first($data['staff_id']);
        if ($staff == null){
            return Response::json(['code'=>1,'msg'=>'请选择本商户的员工']);
        }
        try {
            Project::whereIn('id',$data['project_ids'])->where('merchant_id',$request->user()->merchant_id)->update([
                'owner_user_id' => $staff->id,
                'department_id' => $staff->department_id,
            ]);
            return Response::json(['code'=>0,'msg'=>'分配成功']);
        }catch (\Exception $exception){
            Log::error('分配客户异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'分配失败']);
        }

    }

    /**
     * 导入客户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        //上传文件最大大小,单位M
        $maxSize = 10;
        //支持的上传类型
        $allowed_extensions = ["xls", "xlsx"];
        $file = $request->file('file');
        //检查文件是否上传完成
        if ($file->isValid()){
            //检测类型
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext),$allowed_extensions)){
                return Response::json(['code'=>1,'msg'=>"请上传".implode(",",$allowed_extensions)."格式的文件"]);
            }
            //检测大小
            if ($file->getSize() > $maxSize*1024*1024){
                return Response::json(['code'=>1,'msg'=>"大小限制".$maxSize."M"]);
            }
        }else{
            Log::info('导入项目是文件上传不完整:'.$file->getErrorMessage());
            return Response::json(['code'=>1,'msg'=>'文件上传不完整']);
        }
        $newFile = time()."_".uniqid().".".$file->getClientOriginalExtension();
        try{
            $res = Storage::disk('uploads')->put($newFile,file_get_contents($file->getRealPath()));
        }catch (\Exception $exception){
            Log::info('上传文件失败：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'文件上传失败']);
        }
        $xlsFile = public_path('uploads').'/'.$newFile;
        try{
            Excel::import(new ProjectImport(), $xlsFile);
            return Response::json(['code'=>0,'msg'=>'导入成功']);
        }catch (\Exception $exception){
            Log::info('导入失败：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'导入失败']);
        }
    }

    /**
     * 待分配删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignmentDestroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            DB::table('project')->whereIn('id',$ids)->delete();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
