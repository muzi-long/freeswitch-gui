<?php

namespace App\Imports;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class ProjectImport implements ToModel
{
    public function model(array $row)
    {
        return new Project([
            'company_name' => $row[0],
            'name'     => $row[1],
            'phone'    => $row[2],
            'created_user_id' => Auth::user()->id,
        ]);
    }
}
