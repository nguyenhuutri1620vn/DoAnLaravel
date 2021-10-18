<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Config extends Model
{
    use HasFactory, HasApiTokens;
    protected $table='config';
    protected $fillable = [
        'name',
        'slogan',
        'email',
        'price_ship',
        'phone',
        'address'
    ];
}
