<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';
    //白名單
    protected $fillable = [
       'moneyToLevel','upgrade_limit' 
    ];
    public $timestamps = false;
    
}
