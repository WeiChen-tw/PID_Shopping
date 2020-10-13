<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    use SoftDeletes;
    protected $table ="orders";
    protected $dates = ['deleted_at'];

    //白名單
    protected $fillable = [

        'id','user_id','addr','status','total','created_at'

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
