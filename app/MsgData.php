<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsgData extends Model
{
    protected $table = 'msgData';
    //白名單
    protected $fillable = [

        'id','email', 'content', 

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
