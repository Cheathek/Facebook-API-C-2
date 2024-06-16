<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'image',
        'video',
        'user_id',
        'share',
        'post_id',
        'tags',
    ];
    public function getImagesAttribute($value)
    {
        return json_decode($value, true);
    }
    public static function list()
    {
        $userId = Auth::id();
        $friends = self::where('user_id', $userId)
            ->get();
        return $friends;
    }
    public static function listPostFriend($id)
    {
        $friends = self::where('user_id', $id)
            ->get();
        return $friends;
    }
    public function likes(): HasMany
    {
        return $this->hasMany(LikePost::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class,'id','tags');
    }
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

}
