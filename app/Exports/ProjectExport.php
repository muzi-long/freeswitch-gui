<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectExport implements FromArray,WithHeadings
{
    public function array(): array
    {
        return [

        ];
    }
    public function headings(): array
    {
        return [
            '公司名称','姓名','联系电话'
        ];
    }
}
