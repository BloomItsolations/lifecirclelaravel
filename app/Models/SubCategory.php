<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    public function category(){
        return $this->hasOne('App\Models\Category','id','category_id');
    }
    public function product(){
        return $this->hasMany('App\Models\Category','subcategory_id','id');
    }
}
