<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'users';
    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'phone' ,
        'provinceID',
        'districtID',
        'address',
        'image'
    ]; 

    protected $with = ['province', 'district'];
    public function province(){
        return $this->belongsTo(Province::class, 'provinceID', 'id');
    }

    public function district(){
        return $this->belongsTo(District::class, 'districtID', 'id');
    }

}
