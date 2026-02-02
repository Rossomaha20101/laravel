<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        //$posts = Post::all();
        //$posts = Post::find([1, 2]);
        //$posts = Post::where('id', '>', '2')->get();
        //$posts = Post::inRandomOrder()->limit(3)->get();
        $posts = Post::orderBy('id', 'desc')->get();
        return view('posts.index')->with('posts', $posts);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.show')->with('post', $post);
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit')->with('post', $post);
    }

    public function update($id, Request $request) 
    {
        $data = $request->validate([
            'title' => 'required|min:5',
            'anons' => 'required|min:7',
            'text' => 'required|min:10',
        ]);

        $post = Post::findOrFail($id);
        $post->title = $data['title'];
        $post->anons = $data['anons'];
        $post->text = $data['text'];
        $post->save();

        return redirect()->route('posts.one', $id);
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route('posts');
    }
}
