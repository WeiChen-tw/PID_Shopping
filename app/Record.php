<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'record';
    //白名單
    protected $fillable = [

        'id', 'field', 'src', 'status', 'user_id',

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
