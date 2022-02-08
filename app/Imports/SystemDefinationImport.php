<?php

namespace App\Imports;

use App\SystemDefination;
use Maatwebsite\Excel\Concerns\ToModel;

class SystemDefinationImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($row[0] != null) {
            return new SystemDefination([
                'key'     => 'flavour',
                'value'   => $row[0],
            ]);
        }
    }
}
