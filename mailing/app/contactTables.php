<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contactTables extends Model
{
    protected $table;
    protected $fillable = ['company', 'name', 'email'];

    function __construct(string $nameTable) {
        $this->table=$nameTable;
    }

}
