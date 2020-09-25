<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\ProjectRequest;
use App\Imports\ProjectImport;
use App\Models\Project;
use App\Models\ProjectDesign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class AssignmentController extends Controller
{

    /**
     * 待分配列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $users = User::where('id','!=',config('freeswitch.user_root_id'))->get();
        return View::make('admin.assignment.index',compact('users'));
    }

    public function data(Request $request)
    {
        $user = Auth::user();
        $data = $request->all([
            'name',
            'phone',
            'company_name',
        ]);
        $res = Project::query()
            //姓名
            ->when($data['name'], function ($query) use ($data) {
                return $query->where('name', $data['name']);
            })
            //联系电话
            ->when($data['phone'], function ($query) use ($data) {
                return $query->where('phone', $data['phone']);
            })
            //公司名称
            ->when($data['company_name'], function ($query) use ($data) {
                return $query->where('company_name', '%' . $data['company_name'] . '%');
            })
            ->where('owner_user_id','>=',0)
            ->orderBy('owner_user_id','asc')
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

    /**
     * 导入项目
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
                return Response::json(['code'=>1,'msg'=>"请上传".implode(",",$allowed_extensions)."格式的图片"]);
            }
            //检测大小
            if ($file->getSize() > $maxSize*1024*1024){
                return Response::json(['code'=>1,'msg'=>"大小限制".$maxSize."M"]);
            }
        }else{
            Log::info('导入项目是文件上传不完整:'.$file->getErrorMessage());
            return Response::json(['code'=>1,'msg'=>'文件上传不完整']);
        }
        $newFile = date('Y-m-d')."_".time()."_".uniqid().".".$file->getClientOriginalExtension();
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
     * 分配
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function to(Request $request)
    {
        $ids = $request->get('ids',[]);
        $user_id = $request->get('user_id');
        DB::beginTransaction();
        try{
            DB::table('project')->whereIn('id',$ids)->update([
                'owner_user_id' => $user_id
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'分配成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('分配异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'分配失败']);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        DB::beginTransaction();
        try{
            DB::table('project')->whereIn('id',$ids)->delete();
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 添加项目
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $designs = ProjectDesign::where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('admin.assignment.create',compact('designs'));
    }

    /**
     * 添加项目
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectRequest $request)
    {
        $user = Auth::user();
        $data = $request->all(['company_name','name','phone']);
        $dataInfo = [];
        $fields = ProjectDesign::where('visiable',1)->get();

        foreach ($fields as $d){
            $items = [
                'project_design_id' => $d->id,
                'data' => $request->get($d->field_key),
            ];
            if ($d->field_type=='checkbox'){
                if (!empty($items['data'])){
                    $items['data'] = implode(',',$items['data']);
                }else{
                    $items['data'] = null;
                }
            }
            array_push($dataInfo,$items);
        }
        DB::beginTransaction();
        try{
            $project_id = DB::table('project')->insertGetId([
                'company_name' => $data['company_name'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'created_user_id' => $user->id,
                'owner_user_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            foreach ($dataInfo as $d){
                DB::table('project_design_value')->insert([
                    'project_id' => $project_id,
                    'project_design_id' => $d['project_design_id'],
                    'data' => $d['data'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('添加项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }

    }

    /**
     * 更新项目
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Project::with('designs')->findOrFail($id);
        return View::make('admin.assignment.edit',compact('model'));
    }

    /**
     * 更新项目
     * @param ProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectRequest $request,$id)
    {
        $data = $request->all(['company_name','name','phone']);
        $dataInfo = [];
        $model = Project::with('designs')->findOrFail($id);
        foreach ($model->designs as $d){
            $items = [
                'id' => $d->pivot->id,
                'data' => $request->get($d->field_key),
            ];
            if ($d->field_type=='checkbox'){
                if (!empty($items['data'])){
                    $items['data'] = implode(',',$items['data']);
                }else{
                    $items['data'] = null;
                }
            }
            array_push($dataInfo,$items);
        }

        DB::beginTransaction();
        try{
            DB::table('project')->where('id',$id)->update([
                'company_name' => $data['company_name'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'updated_user_id' => $request->user()->id,
                'updated_at' => Carbon::now(),
            ]);
            foreach ($dataInfo as $d){
                DB::table('project_design_value')->where('id',$d['id'])->update(['data'=>$d['data']]);
            }
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

}
