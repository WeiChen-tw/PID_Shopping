<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products_Categories extends Model
{
    protected $table = 'products_categories';
    //白名單
    protected $fillable = [
       'product_id','category_id' 
    ];
    public $timestamps = false;
    
}
