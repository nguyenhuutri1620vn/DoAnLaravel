<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'product';
    protected $fillable = [
        'id',
        'cateID',
        'producerID', 
        'discountID',
        'name',
        'description',

        'meta_title',
        'meta_keyword',
        'meta_descrip',

        'original_price',
        'selling_price',
        'number',
        'image',
        'video',
        'featured',
        'popular',
        'rate',
        'status',
        
    ];


    protected $with = ['category', 'producer','discount'];

    public function category(){
        return $this->belongsTo(Category::class, 'cateID', 'id');
    }

    public function producer(){
        return $this->belongsTo(Producer::class, 'producerID', 'id');
    }

    public function discount(){
        return $this->belongsTo(Discount::class, 'discountID', 'id');
    }
    
}
