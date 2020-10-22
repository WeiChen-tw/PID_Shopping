<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsgDataDetail extends Model
{
    protected $table = 'msgDataDetails';
    //白名單
    protected $fillable = [

        'id','msgData_id', 'auth','messageFrom_id','content', 

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
