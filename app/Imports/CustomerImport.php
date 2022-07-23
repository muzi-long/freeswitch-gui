<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel
{
    public function model(array $row)
    {
        if (!isset($row[2]) || !preg_match('/\d{7,11}/',$row[2]) ) {
            return null;
        }
        return new Customer([
            'uuid' => uuid_generate(),
            'name' => $row[0],
            'contact_name'     => $row[1],
            'contact_phone'    => $row[2],
            'created_user_id' => request()->user()->id,
            'created_user_nickname' => request()->user()->nickname,
            'owner_user_id' => request()->user()->id,
            'owner_user_nickname' => request()->user()->nickname,
            'status' => 1,
        ]);
    }
}
