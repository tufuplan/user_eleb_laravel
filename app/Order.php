<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'order_code',  'city','area','province','detail','Receiver','phone','order_status','user_id','shop_id'
    ];
}
