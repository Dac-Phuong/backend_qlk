<?php

namespace App\Imports;

use App\Models\ImportFileExecl;
use App\Models\Purchase;
use App\Models\Purchase_item;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportFileExcel implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        
    }
    public function startRow(): int
    {
        return 2;
    }
}
