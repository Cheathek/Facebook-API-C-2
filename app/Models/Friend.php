<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'confirmed'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function friends():HasMany
    {
        return $this->hasMany(User::class, 'friend_id','id');
    }

    public static function list()
    {
        $post= self::all();
        return $post;
    }
    public static function store($request, $id = null)
    {
        $friend = $request->only('user_id', 'friend_id','confirmed');
        $friend = self::updateOrCreate(['id' => $id], $friend);
    }
}
