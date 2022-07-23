<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class BusinessController extends Controller
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
                ->where('owner_user_id','=',$request->user()->id)
                ->where('status','=',2)
                ->orderByDesc('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.business.index');
    }


    public function to(Request $request)
    {
        $ids = $request->get('ids',[]);
        $user = User::where('id',$request->get('user_id'))->first();
        if ($user == null){
            return $this->error('请选择员工');
        }
        DB::beginTransaction();
        try{
            $data = [
                'owner_user_id' => $user->id,
                'owner_user_nickname' => $user->nickname,
                'owner_department_id' => $user->department_id??0,
                'assignment_user_id' => $request->user()->id,
                'assignment_user_nickname' => $request->user()->nickname,
                'status' => 3,
                'status_time' => date('Y-m-d H:i:s'),
            ];
            Customer::query()->whereIn('id',$ids)->update($data);
            push_message(
                'msg',
                ['title'=>'分配客户提醒','content'=>'用户 '.$request->user()->nickname.' 给你分配了 '.count($ids).' 个客户至个人库'],
                [$user->id],
            );
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('分配异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
