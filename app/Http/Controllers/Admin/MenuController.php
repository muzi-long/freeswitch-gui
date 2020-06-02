<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\StoreRequest;
use App\Http\Requests\Admin\Menu\UpdateRequest;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Models\Permission;

class MenuController extends Controller
{
    /**
     * 菜单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Menu::orderBy('sort','asc')->orderBy('id','asc')->paginate($request->get('limit', 30));
            foreach ($res->items() as $re){
                if ($re->type==1){
                    $re->url = $re->url ? $re->url : route($re->route,[],false);
                }
            }
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('admin.menu.index');
    }

    /**
     * 添加菜单
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $menus = Menu::with('childs')->where('parent_id', 0)->get();
        $permissions = Permission::with('childs')->where('parent_id', 0)->get();
        return View::make('admin.menu.create', compact('menus','permissions','menus'));
    }

    /**
     * 添加菜单
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'name',
            'parent_id',
            'route',
            'url',
            'icon',
            'sort',
            'type',
            'permission_id',
        ]);
        if ($data['type']==1){
            if ($data['route'] && $data['url']){
                return Response::json(['code'=>1,'msg'=>'路由与链接只可启用一个']);
            }
            if (!$data['route'] && !$data['url']){
                return Response::json(['code'=>1,'msg'=>'路由与链接至少启用一个']);
            }
        }
        try {
            Menu::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        } catch (\Exception $exception) {
            Log::error('添加菜单异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 更新菜单
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $menus = Menu::with('childs')->where('parent_id', 0)->get();
        $permissions = Permission::with('childs')->where('parent_id', 0)->get();
        return View::make('admin.menu.edit', compact('menu', 'menus','permissions'));
    }

    /**
     * 更新菜单
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $model = Menu::findOrFail($id);
        $data = $request->all([
            'name',
            'parent_id',
            'route',
            'url',
            'icon',
            'sort',
            'type',
            'permission_id',
        ]);
        if ($data['type']==1){
            if ($data['route'] && $data['url']){
                return Response::json(['code'=>1,'msg'=>'路由与链接只可启用一个']);
            }
            if (!$data['route'] && !$data['url']){
                return Response::json(['code'=>1,'msg'=>'路由与链接至少启用一个']);
            }
        }
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        } catch (\Exception $exception) {
            Log::error('更新菜单异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 删除菜单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        $model = Menu::with('childs')->find($ids[0]);
        if (!$model) {
            return Response::json(['code' => 1, 'msg' => '菜单不存在']);
        }
        //如果有子权限，则禁止删除
        if ($model->childs->isNotEmpty()) {
            return Response::json(['code' => 1, 'msg' => '存在子菜单禁止删除']);
        }
        try {
            $model->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            Log::error('删除菜单异常：'.$exception->getMessage());
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }

}
