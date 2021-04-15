<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MerchantController extends Controller
{

    /**
     * 帐户中心-商户资料
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $staff = $request->user();
        $model = Merchant::findOrFail($staff->merchant_id);
        return View::make('frontend.account.merchant.index',compact('staff','model'));
    }

}
