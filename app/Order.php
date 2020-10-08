<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table ="orders";
    //白名單
    protected $fillable = [

        'id','user_id', 'productID', 'price', 'quantity', 'discount'

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
