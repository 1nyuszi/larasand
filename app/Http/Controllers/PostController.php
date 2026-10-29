<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function show(string $slug): View
    {
        $post = Post::query()->published()->with(['user', 'category', 'tags'])->where('slug', $slug)->firstOrFail();

        return view('post', compact('post'));
    }
}
