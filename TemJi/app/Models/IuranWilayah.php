<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IuranWilayah extends Model
{
    protected $fillable = ['wilayah', 'nominal', 'berlaku_sejak'];
}
