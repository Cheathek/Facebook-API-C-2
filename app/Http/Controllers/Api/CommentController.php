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
        return response()->json(Comment::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateComment($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::create($request->all());
        $this->storeAttachment($request, $comment);

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
        $comment = Comment::find($id);
        return response()->json(['success' => true, 'data' => $comment], 200);
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
        $validator = $this->validateComment($request, true);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = Comment::findOrFail($id);
        $comment->update($request->all());
        $this->storeAttachment($request, $comment);

        return response()->json(['success' => true, 'message' => 'updated comment successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $comment = Comment::find($id);
        $comment->delete();
        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'comment delete successfully'
        ], 200);
    }

    private function validateComment(Request $request, $isUpdate = false)
    {
        $rules = [
            'content' => 'required|string|min:3',
            'user_id' => 'required|integer|exists:users,id',
            'post_id' => 'required|integer|exists:posts,id',
            'like_count' => 'nullable|integer|min:0',
        ];

        if ($isUpdate) {
            $rules['attachment'] = 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,video/mp4,application/octet-stream';
        } else {
            $rules['img'] = 'required|file|mimetypes:image/jpeg,image/png,image/gif';
            $rules['video'] = 'nullable|file|mimetypes:video/mp4,application/octet-stream';
            $rules['sticker'] = 'nullable|file|mimetypes:,extension:webp,avif';
        }

        return Validator::make($request->all(), $rules);
    }

    private function storeAttachment(Request $request, Comment $comment)
    {
        if ($request->hasFile('attachment')) {
            $attachment = $this->createAttachment($request->file('attachment'), $comment->id);
            $comment->attachments()->save($attachment);
        }
    }

    private function createAttachment($file, $commentId)
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
