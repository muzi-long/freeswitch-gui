<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectExport;
use App\Http\Requests\Admin\Project\ProjectRequest;
use App\Imports\ProjectImport;
use App\Models\Node;
use App\Models\Project;
use App\Models\ProjectDesign;
use App\Models\ProjectNode;
use App\Models\ProjectRemark;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{

    /**
     * 项目列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $nodes = Node::orderBy('sort','asc')->get();
        $merchants = User::where('id','!=',config('freeswitch.user_root_id'))->get();
        return View::make('admin.project.index',compact('nodes','merchants'));
    }

    public function data(Request $request)
    {
        $user = Auth::user();
        $data = $request->all([
            'name',
            'phone',
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
                if ($user->hasPermissionTo('crm.project.list_all')) {
                    return $query->where('owner_user_id','>',0);
                }elseif ($user->hasPermissionTo('crm.project.list_department')) {
                    $user_ids = User::where('department_id',$user->department_id)->pluck('id')->toArray();
                    return $query->whereIn('owner_user_id',$user_ids);
                }else{
                    return $query->where('owner_user_id',$user->id);
                }
            })
            //姓名
            ->when($data['name'],function ($query) use($data){
                return $query->where('name',$data['name']);
            })
            //联系电话
            ->when($data['phone'],function ($query) use($data){
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
     * 添加项目
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $designs = ProjectDesign::where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('admin.project.create',compact('designs'));
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
                'owner_user_id' => $user->id,
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
        return View::make('admin.project.edit',compact('model'));
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
                'deleted_user_id' => Auth::guard()->user()->id,
                'deleted_at' => Carbon::now(),
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
        return View::make('admin.project.show',compact('model'));
    }

    /**
     * 更新节点
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function node($id)
    {
        $model = Project::findOrFail($id);
        $nodes = Node::orderBy('sort','asc')->get();
        return View::make('admin.project.node',compact('model','nodes'));
    }

    /**
     * 更新节点
     * @param ProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function nodeStore(ProjectRequest $request,$id)
    {
        $model = Project::findOrFail($id);
        $data = $request->all(['node_id','content']);
        $old = $model->node_id;
        $user = Auth::user();
        DB::beginTransaction();
        try{
            DB::table('project_node')->insert([
                'project_id' => $id,
                'old' => $old,
                'new' => $data['node_id'],
                'content' => $data['content'],
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('project')->where('id',$id)->update([
                'node_id' => $data['node_id'],
                'updated_user_id' => $user->id,
                'updated_at' => Carbon::now()
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('更新节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 项目的节点变更记录
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function nodeList(Request $request,$id)
    {
        $res = ProjectNode::with(['oldNode','newNode','user'])
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

    /**
     * 更新备注
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function remark($id)
    {
        $model = Project::findOrFail($id);
        return View::make('admin.project.remark',compact('model'));
    }

    /**
     * 更新备注
     * @param ProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remarkStore(ProjectRequest $request,$id)
    {
        $model = Project::findOrFail($id);
        $data = $request->all(['next_follow_at','content']);
        $user = Auth::user();
        DB::beginTransaction();
        try{
            DB::table('project_remark')->insert([
                'project_id' => $id,
                'content' => $data['content'],
                'next_follow_at' => $data['next_follow_at'],
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('project')->where('id',$id)->update([
                'next_follow_at' => $data['next_follow_at'],
                'follow_at' => Carbon::now(),
                'follow_user_id' => $user->id,
                'updated_user_id' => $user->id,
                'updated_at' => Carbon::now()
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新备注异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }

    }

    /**
     * 备注记录
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remarkList(Request $request,$id)
    {
        $res = ProjectRemark::with(['user'])
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

    /**
     * 下载模板
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(new ProjectExport(),'project.xlsx');
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


}
