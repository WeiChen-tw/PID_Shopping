<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table ="orderDetails";
    //白名單
    protected $fillable = [

        'id','user_id', 'productID', 'price', 'quantity', 'discount'

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
