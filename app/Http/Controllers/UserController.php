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
                /**
     * @OA\Get(
     *     path="/api/friend/list/friend-list/{id}",
     *     tags={"Friends"},
     *     summary="View friends of our friend ",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="string",
     *             enum={"available", "pending", "sold"},
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful show",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function showFriend($id)
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
