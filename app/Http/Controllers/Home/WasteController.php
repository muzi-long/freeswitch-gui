<?php

namespace App\Http\Controllers\Home;

use App\Models\Merchant;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class WasteController extends Controller
{

    public function index()
    {
        return View::make('home.waste.index');
    }

    public function data(Request $request)
    {
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $memberIds = Merchant::where('merchant_id',$merchant_id)->pluck('id');
        $data = $request->all(['company_name','name','phone']);
        $res = Project::onlyTrashed()
            ->where(function ($query) use($memberIds){
                return $query->whereIn('created_merchant_id',$memberIds);
            })
            //公司名称
            ->when($data['company_name'],function ($query) use($data){
                return $query->where('company_name',$data['company_name']);
            })
            //姓名
            ->when($data['name'],function ($query) use($data){
                return $query->where('name',$data['name']);
            })
            //联系电话
            ->when($data['phone'],function ($query) use($data){
                return $query->where('phone',$data['phone']);
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

    public function retrieve(Request $request)
    {
        $id = $request->get('id');
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $memberIds = Merchant::where('merchant_id',$merchant_id)->pluck('id');

        $project = Project::onlyTrashed()
            ->where('id',$id)
            ->whereIn('created_merchant_id',$memberIds)//只能拾回自己商户下的
            ->first();
        if (!$project){
            return Response::json(['code'=>1,'msg'=>'拾回异常：项目不存在']);
        }
        DB::beginTransaction();
        try{
            DB::table('project')->where('id',$id)->update([
                'deleted_merchant_id' => null,
                'deleted_at' => null,
                'created_merchant_id' => $user->id,
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'拾回成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('拾回异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'拾回失败']);
        }

    }

}
