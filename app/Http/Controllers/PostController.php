<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

    public function index()
    {
    }
    public function store(Request $request)
    {
        $post = Post::create($request->all());
        return $post->load("user", "station");
    }
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return $post->load("user", "station", "Comments.user");
    }
    public function update(Request $request, Post $post)
    {
        //
    }
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
    }
}