<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopCart extends Model
{
    protected $table = 'shopCart';
    //白名單
    protected $fillable = [

        'user_id','productID', 'quantity', 'price',

    ];
    public $timestamps = false;
    //public $primaryKey = '';
}
