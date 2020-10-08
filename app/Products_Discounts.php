<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products_Discounts extends Model
{
    protected $table = 'products_discounts';
    //白名單
    protected $fillable = [
       'product_id','discount_id' 
    ];
    public $timestamps = false;
    
}
