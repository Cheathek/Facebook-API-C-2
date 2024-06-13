<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::all();;
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3',
            'img' => 'required|file|mimetypes:image/jpeg,image/png,image/gif',            'video' => 'nullable|file|mimetypes:video/mp4,application/octet-stream',
            'sticker' => 'nullable|file|mimetypes:,extension:webp,avif',
            'user_id' => 'required|integer|exists:users,id',
            'post_id' => 'required|integer|exists:posts,id',
            'like_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'img' => $request->img,
            'sticker' => $request->sticker, 
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'like_count' => $request->like_count ?? 0,
        ]);

        if ($request->hasFile('attachment')) {
            $attachment = $this->storeAttachment($request->file('attachment'), $comment->id);
            $comment->attachments()->save($attachment);
        }

        return response()->json(['success' => true, 'message' => 'created comment successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::with('attachments')->findOrFail($id);
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3',
            'like_count' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,video/mp4,application/octet-stream',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::findOrFail($id);
        $comment->update([
            'content' => $request->content,
            'like_count' => $request->like_count ?? 0,
        ]);

        if ($request->hasFile('attachment')) {
            $attachment = $this->storeAttachment($request->file('attachment'), $comment->id);
            $comment->attachments()->save($attachment);
        }

        return response()->json(['success' => true, 'message' => 'updated comment successfully'],);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.'], 204);
    }

    private function storeAttachment($file, $commentId)
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $file->storeAs('comments', $filename);

        return new Comment([
            'comment_id' => $commentId,
            'type' => $this->getAttachmentType($file),
            'path' => 'comments/' . $filename,
            'filename' => $filename,
        ]);
    }

    private function getAttachmentType($file)
    {
        $mime = $file->getMimeType();
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            return 'image';
        } elseif ($mime === 'video/mp4') {
            return 'video';
        } else {
            return 'sticker';
        }
    }
}
