<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
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
                ->where('status','=',2)
                ->orderByDesc('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.business.index');
    }

}
