<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Content extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'content';
    protected $fillable = [
        'slug',
        'name',
        'description',
        'image',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'status'
    ];
}
