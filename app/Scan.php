<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $fillable = [
        'id', 'scanCount', 'points', 'description'
    ];

    public $incrementing = false;
}
