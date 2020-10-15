<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //白名單
    protected $fillable = [

        'id','name', 'category', 'price', 'img','quantity', 'quantitySold', 'description',

    ];
    //public $timestamps = false;
    public $primaryKey = 'productID';

    
}
