<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CustomerField;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AssignmentController extends Controller
{
    /**
     * 待分配库列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function assignment(Request $request)
    {
        if ($request->ajax()){
            $res = Customer::query()
                ->where('status','=',1)
                ->orderByDesc()
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.assignment.index');
    }

    public function create()
    {
        $fields = CustomerField::query()
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('crm.assignment.create',compact('fields'));
    }

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

}
