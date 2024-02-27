<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinaryBenefit extends Model
{
    use HasFactory;
    
    public function user_details(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function right_user(){
        return $this->hasOne(User::class,'id','right_user_id');
    }
    public function left_user(){
        return $this->hasOne(User::class,'id','left_user_id');
    }
}
