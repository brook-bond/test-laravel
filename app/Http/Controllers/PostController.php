<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->paginate(3);
        return view('post.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required|min:10',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8048',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/images', $imageName);

        $postData = ['title' => $request->title, 'category' => $request->category, 'content' => $request->content, 'image' => $imageName];

        Post::create($postData);
        return redirect('/post')->with(['message' => 'Post added successfully!', 'status' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('post.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('post.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $imageName = '';
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/images', $imageName);
            if ($post->image) {
                Storage::delete('public/images/' . $post->image);
            }
        } else {
            $imageName = $post->image;
        }

        $postData = ['title' => $request->title, 'category' => $request->category, 'content' => $request->content, 'image' => $imageName];

        $post->update($postData);
        return redirect('/post')->with(['message' => 'Post updated successfully!', 'status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Storage::delete('public/images/' . $post->image);
        $post->delete();
        return redirect('/post')->with(['message' => 'Post deleted successfully!', 'status' => 'info']);
    }
}
