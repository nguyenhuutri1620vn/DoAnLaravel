<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Discount extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'discount';
    protected $fillable = [
        'name',
        'percent',
        'status'
    ];
}
