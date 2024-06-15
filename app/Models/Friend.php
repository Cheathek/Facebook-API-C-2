<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'confirmed'];

    public function friends(): HasMany
    {
        return $this->hasMany(User::class, 'friend_id', 'id');
    }

    public static function list()
    {
        $userId = Auth::id();
        $friends = self::where('user_id', $userId)
            ->where('confirmed', true)
            ->orWhere('friend_id', $userId)
            ->where('confirmed', true)
            ->get();

        return $friends;
    }
    public static function requestList()
    {
        $userId = Auth::id();
        $friends = self::where('user_id', $userId)
            ->where('confirmed', false)
            ->orWhere('friend_id', $userId)
            ->where('confirmed', false)
            ->get();

        return $friends;
    }
    public static function store($data, $id = null)
    {
        self::updateOrCreate(['id' => $id], $data);
    }

    
}
