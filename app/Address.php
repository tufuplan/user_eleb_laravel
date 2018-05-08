<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'name',  'Receiver','phone','province','city','area','detail','user_id'
    ];
}
