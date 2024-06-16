<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/post/list",
     *     tags={"posts"},
     *     summary="Get all Posts",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="index",
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
    public function index()
    {
        $posts = Post::list();
        $posts = PostResource::collection($posts);
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    /**
     * @OA\Post(
     *     path="/api/post/create",
     *     tags={"posts"},
     *     summary="Create post",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="store",
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
     *         description="successful created",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user_id = Auth::id();

        if ($request->has('post_id')) {
            $post = Post::find($request->post_id);
            if ($post) {
                $sharedPost = Post::create([
                    'user_id' => $user_id,
                    'post_id' => $request->post_id,
                    'title' => $post->title,
                    'tags' => $post->tags,
                    'share' => true
                ]);
                return response()->json([
                    'message' => 'Post Shared successfully',
                    'post' => $sharedPost,
                ], 201);
            }
        } else {
            $request->validate([
                'title' => 'required|string|max:255',
                'text' => 'required|string',
            ]);

            $imagePaths = [];
            if ($request->hasFile('image')) {
                $images = $request->file('image');
                foreach ($images as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    $imagePaths[] = $imageName;
                }
            }

            $videoPaths = [];
            if ($request->hasFile('video')) {
                $videos = $request->file('video');
                foreach ($videos as $video) {
                    $videoName = time() . '_' . $video->getClientOriginalName();
                    $video->move(public_path('images'), $videoName);
                    $videoPaths[] = $videoName;
                }
            }

            $post = Post::create([
                'user_id' => $user_id,
                'title' => $request->title,
                'text' => $request->text,
                'tags' => $request->tags,
                'image' => json_encode($imagePaths),
                'video' => json_encode($videoPaths),
            ]);
            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post,
            ], 201);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/post/update/image/{id}",
     *     tags={"posts"},
     *     summary="Update image or video",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="updateImage",
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
    public function updateImage(Request $request, $id)
    {
        $user_id = Auth::id();
        $post = Post::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or you are not authorized to update this post.',
            ], 404);
        }

        $imagePaths = [];
        $videoPaths = [];

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            foreach ($images as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }
            $post->image = json_encode($imagePaths);
        }

        if ($request->hasFile('video')) {
            $videos = $request->file('video');
            foreach ($videos as $video) {
                $videoName = time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('images'), $videoName);
                $videoPaths[] = $videoName;
            }
            $post->video = json_encode($videoPaths);
        }

        $post->save();
        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/post/update/{id}",
     *     tags={"posts"},
     *     summary="Update data ",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="update",
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
    public function update(Request $request, $id)
    {
        $user_id = Auth::id();
        $post = Post::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or It is not your post',
            ], 404);
        }

        $data = $request->json()->all();

        if (isset($data['title'])) {
            $post->title = $data['title'];
        }

        if (isset($data['text'])) {
            $post->text = $data['text'];
        }

        $post->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/post/delete/{id}",
     *     tags={"posts"},
     *     summary="Delete Post ",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="deletePost",
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
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to delete this post.'], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ], 200);
    }

     /**
     * @OA\Get(
     *     path="/api/post/show/{id}",
     *     tags={"posts"},
     *     summary="Show Post ",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="showPost",
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
    public function showPost($id)
    {
        $post = Post::findOrFail($id);
        $post = new PostResource($post);
        return response()->json($post);
    }

}
