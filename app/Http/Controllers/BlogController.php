<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Blog;
use App\Models\BlogLike;

class BlogController extends Controller
{
    //
    public function create(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'title' => ['required', 'max:200'],
            'description' => 'required',
            'image' => ['required', 'image', 'mimes:jpg,png'],
            'content' => 'required',
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

        $blog->load('user:id, email, fullname');

        return response()->json([
            'message' => 'Blog Created successfully',
            'data' => $blog
        ], 200);
    }

    public function blogs()
    {
        return Blog::withCount('comments')->withCount('likes')->get();
    }

    public function getSingleBlog($id)
    {
        $blog = Blog::with('user')->find($id);
        if (!$blog) {
            return response()->json([
                'message' => 'Blog with id' . $id . 'not found'
            ], 403);
        }

        return response()->json([
            'message' => 'Blog fetched successfully',
            'data' => $blog
        ], 200);
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
                ], 200);
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

    public function update($id, Request $req)
    {
        $blog = Blog::where('id', $id)->first();
        if ($blog && $blog->user_id == $req->user()->id) {
            $image_name = '';
            $validate = Validator::make($req->all(), [
                'title' => ['required'],
                'description' => 'required',
                'image' => ['nullable', 'image', 'mimes:jpg,png'],
                'content' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            } elseif ($req->hasFile('image')) {
                $image_name = time() . '.' . $req->image->extension();
                $req->image->move(public_path('/uploads/blog_images'), $image_name);
                $old_path = public_path() . 'uploads/blog_images/' . $blog->image;
                if (File::exists($old_path)) {
                    File::delete($old_path);
                } else {
                    $image_name = $blog->image;
                }
            }

            $blog->update([
                'title' => $req->title,
                'description' => $req->description,
                'image' => $image_name,
                'content' => $req->content,
                'user_id' => $req->user()->id
            ]);

            $blog->load('user:id,email,fullname');

            return response()->json([
                'message' => 'Blog Updated successfully',
                'data' => $blog
            ], 200);
        } else {
            return response()->json([
                'message' => 'Blog with id' . $id . 'not found'
            ], 403);
        }
    }

    public function toggleLike($id, Request $req)
    {
        $blog = Blog::where('id', $id)->first();

        if ($blog) {
            $user = $req->user();
            $blog_like = BlogLike::where('blog_id', $blog->id)->where('user_id', $user->id)->first();
            if ($blog_like) {
                $blog_like->delete();
                return response()->json([
                    'message' => 'Like Successfully removed'
                ], 200);
            } else {
                BlogLike::create([
                    'blog_id' => $blog->id,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'message' => 'Liked Successfully'
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'No blog Found'
            ], 403);
        }
    }
}
