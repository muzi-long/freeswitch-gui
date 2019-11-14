<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectDesign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProjectController extends Controller
{

    /**
     * 项目列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('home.project.index');
    }

    public function data(Request $request)
    {
        $user = Auth::guard('merchant')->user();
        $res = Project::with(['node','followMerchant'])->where('follow_merchant_id',$user->id)
            ->orWhere('created_merchant_id',$user->id)
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
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $designs = ProjectDesign::where('merchant_id',$merchant_id)
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('home.project.create',compact('designs'));
    }

    /**
     * 添加项目
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectRequest $request)
    {
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $data = $request->all(['name','phone']);
        $dataInfo = [];
        $fields = ProjectDesign::where('merchant_id',$merchant_id)->where('visiable',1)->get();

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

        try{
            $project = Project::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'created_merchant_id' => $user->id,
            ]);
            if ($project){
                foreach ($dataInfo as $d){
                    DB::table('project_design_value')->insert([
                        'project_id' => $project->id,
                        'project_design_id' => $d['project_design_id'],
                        'data' => $d['data'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
            return Redirect::route('home.project')->with(['success'=>'添加成功']);
        }catch (\Exception $exception){
            Log::info('添加项目异常：'.$exception->getMessage());
            return Redirect::back()->withInput()->withErrors('添加失败');
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
        return View::make('home.project.edit',compact('model'));
    }

    /**
     * 更新项目
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProjectRequest $request,$id)
    {
        $user = Auth::guard('merchant')->user();
        $data = $request->all(['name','phone']);
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
                'name' => $data['name'],
                'phone' => $data['phone'],
                'updated_merchant_id' => $user->id,
                'updated_at' => Carbon::now(),
            ]);
            foreach ($dataInfo as $d){
                DB::table('project_design_value')->where('id',$d['id'])->update(['data'=>$d['data']]);
            }
            DB::commit();
            return Redirect::route('home.project')->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('更新项目异常：'.$exception->getMessage());
            return Redirect::back()->withInput()->withErrors('更新失败');
        }
    }

    /**
     * 删除项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $project = Project::findOrFail($id);
        $ids = $request->get('ids',[]);
        $id = $ids[0];
        DB::beginTransaction();
        try{
            DB::table('project')->where('id',$id)->update([
                'deleted_merchant_id' => Auth::guard('merchant')->user()->id,
                'deleted_at' => Carbon::now(),
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('删除项目异常：'.$exception->getMessage());
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
        return View::make('home.project.show',compact('model'));
    }
    
}
