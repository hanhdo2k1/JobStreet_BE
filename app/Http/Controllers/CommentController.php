<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "token" => "required",
            "content" => "required",
            "post_id" => "required"
        ]);

        $token = $validatedData['token'];
        $post_id = $validatedData['post_id'];
        $content = $validatedData['content'];


        $company = Company::where("token", $token)->first();
        if ($company) {
            $comment = new Comment();
            $comment->company_id = $company->id;
            $comment->post_id = $post_id;
            $comment->content = $content;
            $comment->save();

            return response()->json([
                "status" => 200,
                "message" => "Comment created successfully."
            ]);
        } else {
            $user = User::where("token", $token)->first();

            if ($user) {
                $comment = new Comment();
                $comment->user_id = $user->id;
                $comment->post_id = $post_id;
                $comment->content = $content;
                $comment->save();

                return response()->json([
                    "status" => 200,
                    "message" => "Comment created successfully."
                ]);
            }
        }

        return response()->json([
            "status" => 500,
            "message" => "Failed to create comment."
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user_comment = Comment::leftJoin("users", "comments.user_id", "=", "users.id")
            ->leftJoin("companies", "comments.company_id", "=", "companies.id")
            ->leftJoin("posts", "comments.post_id", "=", "posts.id")
            ->where("comments.post_id", $id)
            ->where(function ($query) {
                $query->whereNotNull("comments.user_id")
                    ->orWhereNotNull("comments.company_id");
            })
            ->select("users.username", "companies.company_name", "users.avatar", "companies.logo", "comments.content")
            ->groupBy("users.id", "users.username", "users.avatar", "companies.id", "companies.company_name", "companies.logo", "comments.content")
            ->get();
        return response()->json($user_comment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
