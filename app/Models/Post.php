<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'auth_id',
        'tags',
    ];
    public static function list()
    {
        $post= self::all();
        return $post;
    }

    public static function store($request, $id = null)
    {
        $post = $request->only('title', 'content', 'auth_id', 'tags');
        $post = self::updateOrCreate(['id' => $id], $post);
    }


}
