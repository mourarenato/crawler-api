<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iso4217 extends Model
{
    use HasFactory;

    protected $table = 'iso4217';

    protected $fillable = [
        'id',
        'code',
        'number',
        'decimal',
        'currency',
        'currency_locations',
    ];

}