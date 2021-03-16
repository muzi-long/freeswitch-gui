<?php
namespace App\Service;


use App\Models\Cdr;
use Illuminate\Support\Facades\Log;

class EslListen
{
    //通话记录对象
    public $cdr;

    //esl连接对象
    public $fs;

    public function __construct($uuid)
    {
        $cdr = Cdr::query()->where('uuid','=',$uuid)->first();
        if ($cdr == null) {
            Log::info(sprintf("通话记录[%s]不存在",$uuid));
            return false;
        }

    }

    public function run()
    {

    }

}
