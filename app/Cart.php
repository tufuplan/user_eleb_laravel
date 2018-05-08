<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'goods_list',  'goods_count','user_id','status'
    ];
}
