<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::leftJoin("users", "posts.user_id", "=", "users.id")
            ->leftJoin("companies", "posts.company_id", "=", "companies.id")
            ->where(function ($query) {
                $query->whereNotNull("posts.user_id")
                    ->orWhereNotNull("posts.company_id");
            })
            ->select("users.username", "companies.company_name", "companies.logo", "posts.title", "posts.body", "posts.id", "users.avatar", "posts.image")
            ->orderBy("posts.created_at", "desc")
            ->get();
        return response()->json($posts);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'title' => 'required',
            'body' => 'required',
            'image' => 'nullable'
        ]);
        
        $token = $request->token;
        $title = $request->title;
        $body = $request->body;
        $post = new Post();
        $user = User::where('token', $token)->first();
        
        if ($user) {
            $post->user_id = $user->id;
        } else {
            $company = Company::where('token', $token)->first();
            $post->company_id = $company->id;
        }
        
        $post->title = $title;
        $post->body = $body;
        
        if ($request->hasFile("image")) {
            $image = $request->file("image");
            $imageName = Str::random(16) . "." . $image->getClientOriginalExtension();
            Storage::disk("public")->put($imageName, file_get_contents($image));
            $post->image = $imageName;
        }
        
        $post->save();
        
        return response()->json(['message' => 'Post created successfully']);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($post);
    }


    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        return response()->json(['message' => 'Post updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
