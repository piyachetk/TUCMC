<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'id', 'name', 'secret', 'email', 'gender', 'scanned', 'points'
    ];

    protected $casts = [
        'scanned' => 'array',
    ];

    public $incrementing = false;
}
