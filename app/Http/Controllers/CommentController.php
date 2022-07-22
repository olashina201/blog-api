<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function createComment($blog_id, Request $req)
    {
        $blog = Blog::where('id', $blog_id);
        if ($blog) {
            $validate = Validator::make($req->all(), [
                'comment' => ['required'],
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            }

            $comment = Comment::create([
                'comment' => $req->comment,
                'blog_id' => $blog_id,
                'user_id' => $req->user()->id
            ]);
            $comment->load('user');

            return response()->json([
                'message' => 'Comment Created successfully',
                'data' => $comment
            ], 200);
        } else {
            return response()->json([
                'message' => 'Blog with id' . $blog_id . 'not found'
            ], 403);
        }
    }
}
