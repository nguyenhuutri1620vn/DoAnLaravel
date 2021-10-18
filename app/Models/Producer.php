<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Producer extends Model
{
    use HasFactory, HasApiTokens;
    public $table = 'producer';
    public $fillable = [
        'meta_title',
        'meta_keyword',
        'meta_descipt',
        'slug',
        'name',
        'description',
        'status',
    ];
}
