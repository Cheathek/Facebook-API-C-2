<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'message'       => 'Login success',
            'data'  => $request->user(),
        ]);
    }
    public function show($id)
{
    $user = User::findOrFail($id);
    $friends = $user->friends()->get();

    return response()->json([
        'success' => true,
        'data' => $friends,
        'message' => 'Friends retrieved successfully'
    ], 200);
}
}
