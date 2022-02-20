<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Comment extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'comment';
    protected $fillable = [
        'userID',
        'productID',
        'content',
        'detail',
        'rate'
    ];

    protected $with = ['product','users'];

    public function product(){
        return $this->belongsTo(Product::class, 'productID', 'id');
    }

    public function users(){
        return $this->belongsTo(Users::class, 'userID', 'id');
    }
}
