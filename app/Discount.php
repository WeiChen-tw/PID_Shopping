<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    //白名單
    protected $fillable = [
        'method', 'total', 'discount',
    ];
    public $timestamps = false;

}
