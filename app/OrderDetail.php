<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrderDetail extends Model
{
    use SoftDeletes;
    protected $table ="orderDetails";
    protected $dates = ['deleted_at'];
    //白名單
    protected $fillable = [

        'id','user_id', 'productID', 'price', 'quantity', 'discount_flag'

    ];
    public $timestamps = true;
    //public $primaryKey = '';
}
