<?php

namespace App\Http\Controllers;

use App\Models\Cdr;
use App\Models\CustomerRemark;
use App\Models\Department;
use App\Models\Node;
use App\Models\Order;
use App\Models\OrderPay;
use App\Models\OrderRemark;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function getPermissionByRoleId(Request $request)
    {
        $role_id = $request->input('role_id');
        $role = null;
        $checkedIds = [];
        if ($role_id) {
            $role = Role::query()->where('id', $role_id)->first();
        }
        $permissions = Permission::query()->orderByDesc('id')->get();
        foreach ($permissions as $permission) {
            if ($role != null) {
                if ($role->hasPermissionTo($permission)) {
                    array_push($checkedIds, $permission->id);
                }
            }
        }
        return $this->success('ok', ['trees' => $permissions, 'checkedId' => $checkedIds]);
    }

    public function getRoleByUserId(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = null;
        if ($user_id) {
            $user = User::query()->where('id', $user_id)->first();
        }
        $roles = Role::query()->orderByDesc('id')->get();
        foreach ($roles as $role) {
            $role->selected = $user != null && $user->hasRole($role);
        }
        return $this->success('ok', $roles);
    }

    public function getDepartmentByUserId(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = null;
        if ($user_id) {
            $user = User::query()->where('id', $user_id)->first();
        }
        $departments = Department::query()->orderByDesc('id')->get();
        foreach ($departments as $d) {
            $d->selected = $user != null && $user->department_id == $d->id;
        }
        $data = recursive($departments);
        return $this->success('ok', $data);
    }

    public function getUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $users = User::query()->get();
        foreach ($users as $user){
            $user->selected = $user_id == $user->id;
        }
        return $this->success('ok',$users);
    }

    public function getNode(Request $request)
    {
        $node_id = $request->input('node_id');
        $type = $request->input('type');
        $nodes = Node::query()
            ->whereIn('type',[1,$type])
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get();
        foreach ($nodes as $node){
            $node->selected = $node_id == $node->id;
        }
        return $this->success('ok',$nodes);
    }


    public function remarkList(Request $request)
    {
        $node_type = $request->input('type');
        $id = $request->input('id');
        if ($node_type == 2){
            $res = CustomerRemark::query()->where('customer_id','=',$id)->orderByDesc('id')->paginate($request->get('limit', 2));
        }elseif ($node_type == 3){
            $res = OrderRemark::query()->where('order_id','=',$id)->orderByDesc('id')->paginate($request->get('limit', 2));
        }
        return $this->success('ok',['list'=>$res->items(),'lastPage'=>$res->lastPage()]);
    }


    /**
     * 呼叫接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function call(Request $request)
    {
        $user_id = $request->input('user_id');
        $callee = $request->input('callee');
        $user_data = $request->input('user_data');
        $user = User::query()->with('sip')->where('id', '=', $user_id)->first();
        if ($user->sip == null) {
            return $this->error('用户未分配外呼号');
        }
        if ($user->sip->status != 1) {
            return $this->error('用户外呼号未在线');
        }
        try {
            $cdr = Cdr::create([
                'uuid' => uuid_generate(),
                'aleg_uuid' => uuid_generate(),
                'bleg_uuid' => uuid_generate(),
                'caller' => $user->sip->username,
                'callee' => $callee,
                'department_id' => $user->department_id,
                'user_id' => $user->id,
                'user_nickname' => $user->nickname,
                'sip_id' => $user->sip->id,
                'user_data' => $user_data,
                'gateway_id' => $user->sip->gateway_id ?? 0,
            ]);
            Redis::rpush(config('freeswitch.redis_key.dial'), $cdr->uuid);
            return $this->success('呼叫成功', [
                'uuid' => $cdr->uuid,
                'call_time' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $exception) {
            Log::error('呼叫异常：' . $exception->getMessage());
            return $this->error('呼叫失败');
        }
    }

    //文件上传
    public function upload(Request $request)
    {

        //上传文件最大大小,单位M
        $maxSize = 10;
        //支持的上传图片类型
        $allowed_extensions = ["png", "jpg", "gif", "xlsx", "xls"];
        $file = $request->file('file');
        //检查文件是否上传完成
        if ($file->isValid()) {
            //检测图片类型
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext), $allowed_extensions)) {
                return $this->success("请上传" . implode(",", $allowed_extensions) . "格式的图片");
            }
            //检测图片大小
            if ($file->getSize() > $maxSize * 1024 * 1024) {
                return $this->success("图片大小限制" . $maxSize . "M");
            }
        } else {
            return $this->error('文件不完整');
        }
        try {
            $newFile = date('Y/m/d/') . uuid_generate() . "." . $file->getClientOriginalExtension();
            $disk = Storage::disk('uploads');
            $res = $disk->put($newFile, file_get_contents($file->getRealPath()));
            if ($res) {
                $data = [
                    'url' => '/uploads/' . $newFile,
                ];
                return $this->success('上传成功', $data);
            } else {
                Log::error('文件上传错误：' . $file->getErrorMessage());
                $this->error('上传失败');
            }
        }catch (\Exception $exception){
            Log::error('文件上传异常：' . $exception->getMessage());
            $this->error('系统异常');
        }

    }


    public function payList(Request $request)
    {
        $id = $request->input('id');
        $res = OrderPay::query()->where('order_id','=',$id)->orderByDesc('id')->paginate($request->get('limit', 2));
        return $this->success('ok',['list'=>$res->items(),'lastPage'=>$res->lastPage()]);
    }

}
