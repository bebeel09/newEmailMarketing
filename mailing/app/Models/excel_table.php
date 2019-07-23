<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class excel_table extends Model
{
    function get_data_excelFile(string $path)
    {
        $excelObject = new PHPExcel();
        $excelFile = $excelObject->load();
    }
}






?>