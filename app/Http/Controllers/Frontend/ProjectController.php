<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Imports\ProjectImport;
use App\Models\Node;
use App\Models\Project;
use App\Models\ProjectDesign;
use App\Models\ProjectFollow;
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
                ->where('merchant_id','=',$request->user()->merchant_id)
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
        $staff = Staff::where('merchant_id',$request->user()->merchant_id)->find($data['staff_id']);
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
        $newFile = md5(time().uniqid()).".".$file->getClientOriginalExtension();
        try{
            $res = Storage::disk('uploads')->put($newFile,file_get_contents($file->getRealPath()));
        }catch (\Exception $exception){
            Log::info('上传文件失败：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'文件上传失败']);
        }
        $xlsFile = public_path('uploads').date('/Y/m/d/').$newFile;
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

    /**
     * 我的客户
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($request->ajax()){
            $data = $request->all([
                'contact_name',
                'contact_phone',
                'follow_user_id',
                'created_user_id',
                'node_id',
                'follow_at_start',
                'follow_at_end',
                'next_follow_at_start',
                'next_follow_at_end',
                'created_at_start',
                'created_at_end',
            ]);
            $res = Project::with(['node','followUser'])
                ->where(function ($query) use($user){
                    if ($user->hasPermissionTo('frontend.crm.project.list_all')) {
                        return $query->where('merchant_id','=',$user->merchant_id);
                    }elseif ($user->hasPermissionTo('frontend.crm.project.list_department')) {
                        $user_ids = Staff::where('department_id',$user->department_id)->pluck('id')->toArray();
                        return $query->whereIn('owner_user_id',$user_ids);
                    }else{
                        return $query->where('owner_user_id',$user->id);
                    }
                })
                ->where('owner_user_id','>',0)
                //姓名
                ->when($data['contact_name'],function ($query) use($data){
                    return $query->where('name',$data['name']);
                })
                //联系电话
                ->when($data['contact_phone'],function ($query) use($data){
                    return $query->where('phone',$data['phone']);
                })
                //节点
                ->when($data['node_id'],function ($query) use($data){
                    return $query->where('node_id',$data['node_id']);
                })
                //跟进时间
                ->when($data['follow_at_start']&&!$data['follow_at_end'],function ($query) use($data){
                    return $query->where('follow_at','>=',$data['follow_at_start']);
                })
                ->when(!$data['follow_at_start']&&$data['follow_at_end'],function ($query) use($data){
                    return $query->where('follow_at','<=',$data['follow_at_end']);
                })
                ->when($data['follow_at_start']&&$data['follow_at_end'],function ($query) use($data){
                    return $query->whereBetween('follow_at',[$data['follow_at_start'],$data['follow_at_end']]);
                })
                //下次跟进时间
                ->when($data['next_follow_at_start']&&!$data['next_follow_at_end'],function ($query) use($data){
                    return $query->where('next_follow_at','>=',$data['next_follow_at_start']);
                })
                ->when(!$data['next_follow_at_start']&&$data['next_follow_at_end'],function ($query) use($data){
                    return $query->where('next_follow_at','<=',$data['next_follow_at_end']);
                })
                ->when($data['next_follow_at_start']&&$data['next_follow_at_end'],function ($query) use($data){
                    return $query->whereBetween('next_follow_at',[$data['next_follow_at_start'],$data['next_follow_at_end']]);
                })
                //创建时间
                ->when($data['created_at_start']&&!$data['created_at_end'],function ($query) use($data){
                    return $query->where('created_at','>=',$data['created_at_start']);
                })
                ->when(!$data['created_at_start']&&$data['created_at_end'],function ($query) use($data){
                    return $query->where('created_at','<=',$data['created_at_end']);
                })
                ->when($data['created_at_start']&&$data['created_at_end'],function ($query) use($data){
                    return $query->whereBetween('created_at',[$data['created_at_start'],$data['created_at_end']]);
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
        $nodes = Node::where('merchant_id',$request->user()->merchant_id)->get();
        if ($user->hasPermissionTo('frontend.crm.project.list_all')) {
            $users = Staff::where('merchant_id',$user->merchant_id)
                ->where('is_merchant',0)
                ->get();
        }elseif ($user->hasPermissionTo('frontend.crm.project.list_department')) {
            $users = Staff::where('merchant_id',$user->merchant_id)
                ->where('department_id',$user->department_id)
                ->where('is_merchant',0)
                ->get();
        }else{
            $users = Staff::where('merchant_id',$user->merchant_id)
                ->where('department_id',$user->department_id)
                ->where('id',$user->id)
                ->where('is_merchant',0)
                ->get();
        }
        return View::make('frontend.crm.project.index',compact('nodes','users'));
    }

    /**
     * 添加客户
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $designs = ProjectDesign::where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('frontend.crm.project.create',compact('designs'));
    }

    /**
     * 添加客户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->all(['company_name','contact_name','contact_phone']);
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
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'created_user_id' => $user->id,
                'owner_user_id' => $user->id,
                'department_id' => $user->department_id,
                'merchant_id' => $user->merchant_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            foreach ($dataInfo as $d){
                DB::table('project_design_value')->insert([
                    'project_id' => $project_id,
                    'project_design_id' => $d['project_design_id'],
                    'data' => $d['data'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('frontend.crm.project')]);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('添加项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 编辑客户
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Project::with('designs')->findOrFail($id);
        return View::make('frontend.crm.project.edit',compact('model'));
    }

    /**
     * 编辑客户
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,$id)
    {
        $data = $request->all(['company_name','contact_name','contact_phone']);
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
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'updated_user_id' => $request->user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            foreach ($dataInfo as $d){
                DB::table('project_design_value')->where('id',$d['id'])->update(['data'=>$d['data']]);
            }
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('frontend.crm.project')]);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 删除项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        $id = $ids[0];
        DB::beginTransaction();
        try{
            DB::table('project')->where('id',$id)->update([
                'owner_user_id' => -1,
                'deleted_user_id' => $request->user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 项目详情
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $model = Project::with('designs')->findOrFail($id);
        return View::make('frontend.crm.project.show',compact('model'));
    }

    public function follow(Request $request,$id)
    {
        $user = $request->user();
        $model = Project::findOrFail($id);
        if ($request->ajax()){
            $data = $request->all(['new_node_id','next_follow_at','content']);
            DB::beginTransaction();
            $old_node_id = $model->node_id;
            try{
                DB::table('project_follow')->insert([
                    'project_id' => $id,
                    'content' => $data['content'],
                    'next_follow_at' => $data['next_follow_at'],
                    'staff_id' => $user->id,
                    'old_node_id' => $old_node_id,
                    'old_node_name' => Node::where('id',$old_node_id)->value('name'),
                    'new_node_id' => $data['new_node_id'],
                    'new_node_name' => Node::where('id',$data['new_node_id'])->value('name'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                DB::table('project')->where('id',$id)->update([
                    'next_follow_at' => $data['next_follow_at'],
                    'node_id' => $data['new_node_id'],
                    'follow_at' => date('Y-m-d H:i:s'),
                    'follow_user_id' => $user->id,
                    'updated_user_id' => $user->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                DB::commit();
                return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('frontend.crm.project.follow',['id' => $id])]);
            }catch (\Exception $exception){
                DB::rollBack();
                Log::error('更新备注异常：'.$exception->getMessage());
                return Response::json(['code'=>1,'msg'=>'更新失败']);
            }
        }
        $nodes = Node::where('merchant_id',$user->merchant_id)->orderBy('sort','asc')->get();
        return View::make('frontend.crm.project.follow',compact('model','nodes'));
    }

    /**
     * 跟进列表
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function followList(Request $request,$id)
    {
        $res = ProjectFollow::with(['staff'])
            ->where('project_id',$id)
            ->orderByDesc('id')
            ->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return Response::json($data);
    }

}
