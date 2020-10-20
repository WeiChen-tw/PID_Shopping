<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReMsgData extends Model
{
    protected $table = 'reMsgData';
    //白名單
    protected $fillable = [

        'id','email', 'content', 

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
