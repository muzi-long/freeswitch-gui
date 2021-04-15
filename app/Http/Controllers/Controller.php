<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success(string $msg = '操作成功', $data = [], int $count = 0)
    {
        return Response::json(['code' => 0, 'msg' => $msg, 'data' => $data, 'count' => $count]);
    }

    public function error(string $msg = '操作失败')
    {
        return Response::json(['code' => 1, 'msg' => $msg]);
    }
}
