<?php

namespace App\Http\Controllers;

use App\Http\Resources\LikePostResource;
use App\Models\LikePost;
use App\Models\LikeType;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LikePostController extends Controller
{
         /**
     * @OA\Get(
     *     path="/api/like/post/list",
     *     tags={"Likes/Post"},
     *     summary="Show list of like ",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="listLikePost",
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
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function listLikePost()
    {
        $user = Auth::id();

        $likes = LikePost::with('post')
            ->where('user_id', $user)
            ->get();
        $count = LikePost::with('post')
            ->where('user_id', $user)
            ->count();
            $likes = LikePostResource::collection($likes);

        return response()->json([
            'posts' => $likes,
            'count' => $count,
        ]);
    }

           /**
     * @OA\Post(
     *     path="/api/like/post/create",
     *     tags={"Likes/Post"},
     *     summary="Like post",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="likePost",
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
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function likePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = Auth::id();
        $post_id = $request->post_id;
        $existingLike = LikePost::where('post_id', $post_id)
            ->where('user_id', $user)
            ->first();

        if ($existingLike) {
            return response()->json([
                'message' => 'You have already liked this post',
                'like' => $existingLike,
            ], 400);
        }

        $likeType = LikeType::where('name', 'like')->first();
        $like = LikePost::create([
            'post_id' => $post_id,
            'user_id' => $user,
            'like_type_id' => $likeType->id,
        ]);

        return response()->json([
            'message' => 'Post liked',
            'like' => $like,
        ]);
    }

         /**
     * @OA\Delete(
     *     path="/api/like/post/delete/{id}",
     *     tags={"Likes/Post"},
     *     summary="Unlike post",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="unlikePost",
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
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function unlikePost(Request $request, $id)
    {
        $user = Auth::id();
        $post_id = $request->post_id;

        $like = LikePost::where('id', $id)
            ->where('user_id', $user)
            ->where('post_id', $post_id)
            ->first();

        if (!$like) {
            return response()->json([
                'message' => 'No like found',
            ], 404);
        }

        $like->delete();

        return response()->json([
            'message' => 'Post unliked',
        ]);
    }

    
         /**
     * @OA\Put(
     *     path="/api/like/post/update/reach/{id}",
     *     tags={"Likes/Post"},
     *     summary="Change type of reach",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="updateReach",
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
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function updateReach(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'like_type_id' => 'required|integer',
        'post_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 400);
    }

    $post = Post::findOrFail($id);
    // dd($post->id);
    $user_id = Auth::id();
    $post_id = $request->post_id;

    $like = LikePost::where('post_id', $post->id)
                   ->where('user_id', $user_id)
                   ->where('post_id', $post_id)
                   ->first();
    if ($like) {
        $like->like_type_id = $request->like_type_id;
        $like->save();
    }

    return response()->json([
        'message' => 'Post reach and likes updated successfully',
        'post' => $post,
    ]);
}
}
