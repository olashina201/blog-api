<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    //
    public function create(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'title' => ['required', 'max:200'],
            'description' => 'required',
            'image' => ['required', 'image', 'mimes:jpg,png'],
            'content' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'validation fails',
                'error' => $validate->errors()
            ], 422);
        }

        $image_name = time() . '.' . $req->image->extension();
        $req->image->move(public_path('/uploads/blog_images'), $image_name);

        $blog = Blog::create([
            'title' => $req->title,
            'description' => $req->description,
            'image' => $image_name,
            'content' => $req->content,
            'user_id' => $req->user()->id
        ]);

        $blog->load('user:id,email,name');

        return response()->json([
            'message' => 'Blog Created successfully',
            'data' => $blog
        ], 200);
    }

    public function blogs()
    {
        return Blog::all();
    }

    public function getSingleBlog($id)
    {
        return Blog::find($id);
    }

    public function destroy($id, Request $req)
    {
        $blog = Blog::where('id', $id)->first();

        if ($blog) {
            if ($blog->user_id == $req->user()->id) {
                $old_path = public_path() . 'uploads/blog_images/' . $blog->image;
                if (File::exists($old_path)) {
                    File::delete($old_path);
                }
                $blog->delete();
                return response()->json([
                    'message' => 'Blog deleted successfully',
                    'data' => $blog
                ], 403);
            } else {
                return response()->json([
                    'message' => 'Access denied'
                ], 403);
            }
        } else {
            return response()->json([
                'message' => 'No blog Found'
            ], 403);
        }
    }
}
