<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class GrabController extends Controller
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
                ->where('department_id','=',$request->user()->department_id)
                ->where('status','=',4)
                ->orderByDesc('status_time')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.grab.index');
    }

    public function store(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $model = Customer::query()
            ->where('id','=',$customer_id)
            ->where('status','=',4)
            ->first();
        if ($model == null){
            return $this->error('很遗憾已被其它用户抢得');
        }
        try {
            $model->update([
                'owner_user_id' => $request->user()->id,
                'owner_user_nickname' => $request->user()->nickname,
                'status' => 3,
                'status_time' => date('Y-m-d H:i:s'),
            ]);
            return $this->success('抢单成功，请在个人库里查看');
        }catch (\Exception $exception){
            Log::error('抢单异常：'.$exception->getMessage());
            return $this->error('抢单失败');
        }
    }

}
