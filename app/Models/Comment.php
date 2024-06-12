<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content','img','user_id','post_id','like_count'];

    public function user()
    {
        return $this->belongsTo(User::class,'auth_id','id');
    }
}
