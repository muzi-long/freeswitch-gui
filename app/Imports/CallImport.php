<?php

namespace App\Imports;

use App\Models\Call;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class CallImport implements ToModel
{

    public $task_id;

    public function __construct($task_id)
    {
        $this->task_id = $task_id;
    }

    public function model(array $row)
    {
        if (!isset($row[0]) || !preg_match('/\d{7,11}/',$row[0]) ) {
            return null;
        }
        return new Call([
            'task_id' => $this->task_id,
            'phone' => $row[0],
        ]);
    }
}
