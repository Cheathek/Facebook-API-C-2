<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\LikeCommentResource;
use App\Models\Comment;
use App\Models\LikeComment;
use App\Models\LikeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeCommentController extends Controller
{

    
         /**
     * @OA\Get(
     *     path="/api/like/comment/list",
     *     tags={"Likes/Comment"},
     *     summary="List reach in comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="listLikeComment",
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
    public function listLikeComment()
    {
        $user = Auth::id();

        $likes = LikeComment::with('comment')
            ->where('user_id', $user)
            ->get();
        $likes = LikeCommentResource::collection($likes);
        $count = LikeComment::with('comment')
            ->where('user_id', $user)
            ->count();

        return response()->json([
            'comment' => $likes,
            'count' => $count,
        ]);
    }

       /**
     * @OA\Post(
     *     path="/api/like/comment/create",
     *     tags={"Likes/Comment"},
     *     summary="Like in comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="LikeComment",
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
    public function LikeComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = Auth::id();
        $comment_id = $request->comment_id;

        $existingLike = LikeComment::where('comment_id', $comment_id)
            ->where('user_id', $user)
            ->first();

        if ($existingLike) {
            return response()->json([
                'message' => 'You have already liked this comment',
                'like' => $existingLike,
            ], 400);
        }

        $likeType = LikeType::where('name', 'like')->first();
        $like = LikeComment::create([
            'comment_id' => $comment_id,
            'user_id' => $user,
            'like_type_id' => $likeType->id,
        ]);

        return response()->json([
            'message' => 'comment liked',
            'like' => $like,
        ]);
    }

      /**
     * @OA\Delete(
     *     path="/api/like/comment/delete/{id}",
     *     tags={"Likes/Comment"},
     *     summary="Unlike in comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="UnlikeComment",
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
    public function UnlikeComment(Request $request, $id)
    {
        $user = Auth::id();
        $comment_id = $request->comment_id;

        $like = LikeComment::where('id', $id)
            ->where('user_id', $user)
            ->where('comment_id', $comment_id)
            ->first();

        if (!$like) {
            return response()->json([
                'message' => 'No like found',
            ], 404);
        }

        $like->delete();

        return response()->json([
            'message' => 'Comment unliked',
        ]);
    }

    
      /**
     * @OA\Put(
     *     path="/api/like/comment/update/reach/{id}",
     *     tags={"Likes/Comment"},
     *     summary="Change type of reach in comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="updateReachComment",
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
    public function updateReachComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'like_type_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $comment = LikeComment::findOrFail($id);
        $user_id = Auth::id();

        $like = LikeComment::where('id', $comment->id)
            ->where('user_id', $user_id)
            ->first();
        if ($like) {
            $like->like_type_id = $request->like_type_id;
            $like->save();
        }


        return response()->json([
            'message' => 'Comment reach and likes updated successfully',
            'post' => $like,
        ]);
    }
}
