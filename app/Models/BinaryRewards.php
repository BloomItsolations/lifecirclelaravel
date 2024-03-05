<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BinaryRewards extends Model
{
    use HasFactory, SoftDeletes;
    public function user_details(){
        return $this->hasOne(User::class,'id','buyer_id');
    }
}
