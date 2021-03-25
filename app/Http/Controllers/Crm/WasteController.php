<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class WasteController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all(['name','contact_name','contact_phone']);
            $res = Customer::query()
                //客户名称
                ->when($data['name'], function ($query) use ($data) {
                    return $query->where('name', $data['name']);
                })
                //联系电话
                ->when($data['contact_phone'], function ($query) use ($data) {
                    return $query->where('contact_phone', $data['contact_phone']);
                })
                //联系人
                ->when($data['contact_name'], function ($query) use ($data) {
                    return $query->where('contact_name', $data['contact_name'] );
                })
                ->where('status','=',5)
                ->orderByDesc('status_time')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.waste.index');
    }


    public function retrieve(Request $request)
    {
        $id = $request->get('id');
        $model = Customer::query()
            ->where('status','=',5)
            ->where('owner_user_id','=',0)
            ->where('id','=',$id)
            ->first();
        if (!$model){
            return $this->error('拾回失败，已被其它人拾取');
        }
        DB::beginTransaction();
        try{
            $model->update([
                    'owner_user_id' => $request->user()->id,
                    'owner_user_nickname' => $request->user()->nickname,
                    'owner_department_id' => $request->user()->department_id,
                    'status' => 3,
                    'status_time' => date('Y-m-d H:i:s'),
                ]);
            DB::commit();
            return $this->success('拾回成功');
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('拾回异常：'.$exception->getMessage());
            return $this->error('系统异常');
        }
    }


    public function show(Request $request,$id)
    {
        $model = Customer::with('fields')->where('id','=',$id)->first();
        return View::make('crm.waste.show',compact('model'));
    }


    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        try {
            Customer::query()
                ->whereIn('id',$ids)
                ->where('status','=',5)
                ->delete();
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除公海库客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
