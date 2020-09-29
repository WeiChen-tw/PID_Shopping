<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    //白名單
    protected $fillable = [
       'name' 
    ];
    public $timestamps = false;
    
}
