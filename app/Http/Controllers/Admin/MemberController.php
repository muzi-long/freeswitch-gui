<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\Sip;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class MemberController extends Controller
{
    /**
     * 员工列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $merchants = Merchant::with('info')->where('merchant_id',0)->get();
        return View::make('admin.member.index',compact('merchants'));
    }

    /**
     * 员工列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $data = $request->all(['username','status','merchant_id']);
        $res = Merchant::with(['merchant','sip'])
            ->where('merchant_id','!=',0)
            ->when($data['username'],function ($query) use ($data){
                return $query->where('username','like','%'.$data['username'].'%');
            })
            ->when($data['status']!==null,function ($query) use ($data){
                return $query->where('status',$data['status']);
            })
            ->when($data['merchant_id'],function ($query) use ($data){
                return $query->where('merchant_id',$data['merchant_id']);
            })
            ->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * 添加员工
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $merchants = Merchant::with('info')->where('merchant_id',0)->get();
        return View::make('admin.member.create',compact('merchants'));
    }

    /**
     * 添加员工
     * @param MerchantCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MerchantCreateRequest $request)
    {
        $data = $request->all([
            'merchant_id',
            'username',
            'password',
            'contact_name',
            'contact_phone',
            'status',
        ]);
        $data['uuid'] = Uuid::uuid();
        $data['password'] = bcrypt($data['password']);
        //验证最大员工数
        $merchant = Merchant::with('info')->withCount('members')->findOrFail($data['merchant_id']);
        if ($merchant->members_count >= $merchant->info->member_num){
            return back()->withInput()->withErrors('添加失败：超过商户最大员工数量');
        }
        try{
            Merchant::create($data);
            return redirect()->to(route('admin.member'))->with(['success'=>'添加成功']);
        }catch (\Exception $e){
            Log::info('添加员工异常：'.$e->getMessage());
            return back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 更新员工
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Merchant::findOrFail($id);
        $merchants = Merchant::with('info')->where('merchant_id',0)->get();
        return View::make('admin.member.edit',compact('model','merchants'));
    }

    /**
     * 更新员工
     * @param MerchantUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MerchantUpdateRequest $request,$id)
    {
        $model = Merchant::findOrFail($id);
        $data = $request->all([
            'merchant_id',
            'username',
            'password',
            'contact_name',
            'contact_phone',
            'status',
        ]);
        try{
            if ($data['password']==null){
                unset($data['password']);
            }else{
                $data['password'] = bcrypt($data['password']);
            }
            $model->update($data);
            return redirect()->to(route('admin.member'))->with(['success'=>'更新成功']);
        }catch (\Exception $e){
            Log::info('更新员工异常：'.$e->getMessage());
            return back()->withInput()->withErrors('更新失败');
        }
    }

    /**
     * 删除员工
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        //验证参数
        if (!is_array($ids)||empty($ids)){
            return response()->json(['code'=>1, 'msg'=>'请选择删除项']);
        }
        //删除
        try{
            Merchant::whereIn('id',$ids)->delete();
            return response()->json(['code'=>0, 'msg'=>'删除成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1, 'msg'=>$exception->getMessage()]);
        }
    }

    public function assignSip(Request $request)
    {
        $data = $request->all(['id','sip_id']);
        $member = Merchant::findOrFail($data['id']);
        //为空时表示移除绑定分机
        if ($data['sip_id']==null){
            try{
                $member->update(['sip_id'=>0]);
                return Response::json(['code'=>0,'msg'=>'解绑成功']);
            }catch (\Exception $exception){
                Log::info('为员工解绑分机异常：'.$exception->getMessage());
                return Response::json(['code'=>1,'msg'=>'解绑失败']);
            }
        }
        //分机号是否存在
        $sip = Sip::where('merchant_id',$member->merchant_id)
            ->where('username',$data['sip_id'])
            ->first();
        if ($sip==null){
            return Response::json(['code'=>1,'msg'=>'分机号不存在']);
        }
        //分机号是否已被使用
        if (Merchant::where('sip_id',$sip->id)->count()){
            return Response::json(['code'=>1,'msg'=>'分机号已被其它人使用']);
        }
        try{
            $member->update(['sip_id'=>$sip->id]);
            return Response::json(['code'=>0,'msg'=>'绑定成功']);
        }catch (\Exception $exception){
            Log::info('为员工绑定分机异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'绑定失败']);
        }

    }

    /**
     * 更新角色
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function role($id)
    {
        $member = Merchant::findOrFail($id);
        $roles = Role::where('guard_name','merchant')->get();
        foreach ($roles as $role){
            $role->own = $member->hasRole($role) ? true : false;
        }
        return View::make('admin.member.role',compact('member','roles'));
    }

    /**
     * 更新角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRole(Request $request,$id)
    {
        $member = Merchant::findOrFail($id);
        $roles = $request->get('roles',[]);
        try{
            $member->syncRoles($roles);
            return Redirect::route('admin.member')->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            return Redirect::back()->withErrors('更新失败');
        }
    }


}
