<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

       /**
     * @OA\Get(
     *     path="/api/comment/list",
     *     tags={"Comments"},
     *     summary="Show  all comment of user",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="listComment",
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
    public function listComment()
    {
        $userId = Auth::id();
        $comments = Comment::where('user_id', $userId)->get();
        $comments = CommentResource::collection($comments);
        return response()->json(['success' => true, 'comments' => $comments], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



        /**
     * @OA\Post(
     *     path="/api/comment/create",
     *     tags={"Comments"},
     *     summary="Create comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="storeComment",
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
    public function storeComment(Request $request)
    {
        $validator = $this->validateComment($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::id();


        $comment = Comment::create([
            'user_id' => $user,
            'post_id' => $request->post_id,
            'content' => $request->content,
            'img' => $request->img,
            'sticker' => $request->sticker
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => $comment->load('user')
        ], 201);
    }

         /**
     * @OA\Put(
     *     path="/api/comment/update/{id}",
     *     tags={"Comments"},
     *     summary="Update comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="updateComment",
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
    public function updateComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found ',

            ], 201);
        }
        $user_id = Auth::id();
        $theComment = Comment::where('user_id', $user_id)
            ->where('post_id', $comment->id)
            ->where('user_id', $user_id)
            ->first();

        if ($theComment) {

            $comment->update([
                'content' => $request->content,
                'sticker' => $request->sticker
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
            ], 200);
        }
    }

             /**
     * @OA\Delete(
     *     path="/api/comment/delete/{id}",
     *     tags={"Comments"},
     *     summary="Delete comment",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="destroyComment",
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
    public function destroyComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found '
            ], 201);

        }
        $user_id = Auth::id();
        $theComment = Comment::where('id',$comment->id)
                            ->where('user_id', $user_id)
                            ->first();
        if($theComment){
            $comment->delete();
            return response()->json([
               'success' => true,
               'message' => 'Comment deleted successfully'
            ], 200);
        }
    }


    protected function validateComment(Request $request)
    {
        return Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'img' => 'nullable|file|max:2048'
        ]);
    }

    protected function storeAttachment(Request $request)
    {
        if ($request->hasFile('img')) {
            $attachment = $request->file('img');
            $fileName = time() . '_' . $attachment->getClientOriginalName();
            $attachment->storeAs('public/images', $fileName);
            return $fileName;
        }
        return null;
    }
}