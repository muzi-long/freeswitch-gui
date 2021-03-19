<?php

namespace App\Http\Controllers\Call;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Sip;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SipController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Sip::query()->with('user');
            $username = $request->get('username');
            if ($username) {
                $query = $query->where('username', $username);
            }
            $res = $query->orderByDesc('id')->paginate($request->get('limit', 30));
            return $this->success('ok', $res->items(), $res->total());
        }
        return View::make('call.sip.index');
    }

    public function create()
    {
        $gateways = Gateway::query()->select(['id', 'name'])->get();
        return View::make('call.sip.create', compact('gateways'));
    }

    public function store(Request $request)
    {
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
        ]);
        try {
            Sip::create($data);
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('添加分机异常：' . $exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Sip::findOrFail($id);
        $gateways = Gateway::select(['id', 'name'])->get();
        return View::make('call.sip.edit', compact('model', 'gateways'));
    }

    public function update(Request $request, $id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
        ]);
        try {
            $model->update($data);
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('更新分机异常：' . $exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->error('请选择删除项');
        }
        try {
            Sip::destroy($ids);
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('删除分机异常：' . $exception->getMessage());
            return $this->error();
        }
    }

    public function createList()
    {
        $gateways = Gateway::select(['id', 'name'])->get();
        return View::make('call.sip.create_list', compact('gateways'));
    }

    public function storeList(Request $request)
    {
        $data = $request->all(['sip_start', 'sip_end', 'password', 'gateway_id']);
        if ($data['sip_start'] <= $data['sip_end']) {
            //开启事务
            DB::beginTransaction();
            try {
                for ($i = $data['sip_start']; $i <= $data['sip_end']; $i++) {
                    DB::table('sip')->insert([
                        'gateway_id' => $data['gateway_id'],
                        'username' => $i,
                        'password' => $data['password'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                DB::commit();
                return $this->success();
            } catch (\Exception $exception) {
                DB::rollback();
                Log::error('批量添加分机异常：' . $exception->getMessage());
                return $this->error();
            }
        }
        return $this->error('开始分机号必须小于等于结束分机号');
    }

    public function updateXml()
    {
        $sips = DB::table('sip')->get()->toArray();
        try {
            $client = new Client();
            $client->post(config('freeswitch.swoole_http_url.directory'),
                [
                    'json' => $sips,
                    'timeout' => 30
                ]
            );
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('更新分机配置异常：' . $exception->getMessage());
            return $this->error('更新失败');
        }
    }

}
