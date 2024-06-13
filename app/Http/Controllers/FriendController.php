<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $friends = Friend::list();
        return response()->json(['success' => true, 'friends' => $friends]);
    }
    public function indexRequest()
    {
        $friends = Friend::requestList();
        return response()->json(['success' => true, 'friends' => $friends]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userId = Auth::id();
        $friendId = $request->friend_id;

        $existingFriendship = Friend::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->orWhere('user_id', $friendId)
            ->where('friend_id', $userId)
            ->first();

        if ($existingFriendship) {
            return response()->json([
                'success' => false,
                'message' => 'You are already friends with this user.'
            ], 400);
        }
        if ($friendId == $userId) {
            return response()->json([
                'success' => false,
                'message' => 'You can not add friend with yourself'
            ], 400);
        }

        $friendshipData = [
            'user_id' => $userId,
            'friend_id' => $friendId,
            'confirmed' => false,
        ];

        Friend::create($friendshipData);

        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'Friend requested successfully'
        ], 200);
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userId = Auth::id();
        $friendId = $request->user_id;
        $friendship = Friend::where('user_id', $friendId)
            ->where('friend_id', $userId)
            ->where('confirmed', false)
            ->first();

        if (!$friendship) {
            return response()->json([
                'success' => false,
                'data' => false,
                'message' => 'You are a requester to other so can not confirm'
            ], 404);
        }

        $friendship->confirmed = true;
        $friendship->save();

        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'Friend request confirmed successfully'
        ], 200);

    }

    public function removeFriendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required_without:friend_id|exists:users,id',
            'friend_id' => 'required_without:user_id|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userId = Auth::id();
        if ($request->has('user_id')) {
            $friendId = $request->user_id;
            $friendship = Friend::where('friend_id', $userId)
                ->where('user_id', $friendId)
                ->where('confirmed', false)
                ->first();

            if ($friendship) {
                $friendship->delete();
                return response()->json([
                    'success' => true,
                    'data' => true,
                    'message' => 'Friend request removed successfully'
                ], 200);
            }

        } elseif ($request->has('friend_id')) {
            $friendId = $request->friend_id;
            $friendship = Friend::where('user_id', $userId)
                ->where('friend_id', $friendId)
                ->first();
            if ($friendship) {
                $friendship->delete();
                if ($friendship->confirmed = false) {

                    return response()->json([
                        'success' => true,
                        'data' => true,
                        'message' => 'Friend request cancelled successfully'
                    ], 200);
                } else {

                }
                return response()->json([
                    'success' => true,
                    'data' => true,
                    'message' => 'Friend deleted successfully'
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'data' => false,
            'message' => 'Friend request not found'
        ], 404);
    }
   
}
