<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'alternate_mobile',
        'door_no',
        'street_name',
        'city',
        'pincode',
        'state',
    ];
}
