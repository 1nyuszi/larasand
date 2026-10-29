<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __invoke(Request $request, ?Category $category = null, ?Tag $tag = null): View
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:200']]);
        $search = trim($validated['q'] ?? '');
        $posts = Post::query()->published()->with(['user', 'category', 'tags'])
            ->when($category, fn (Builder $query) => $query->whereBelongsTo($category))
            ->when($tag, fn (Builder $query) => $query->whereHas('tags', fn (Builder $tags) => $tags->whereKey($tag->id)))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%')->orWhere('content', 'like', '%'.$search.'%');
            }))
            ->orderByDesc('published_at')->orderByDesc('id')->paginate(10)->withQueryString();

        return view('blog', [
            'posts' => $posts,
            'search' => $search,
            'category' => $category,
            'tag' => $tag,
            'featuredPosts' => Post::query()->published()->where('is_featured', true)->latest('published_at')->limit(3)->get(),
            'categories' => Category::query()->whereHas('posts', fn (Builder $query) => $query->published())->withCount(['posts' => fn (Builder $query) => $query->published()])->orderBy('name')->get(),
            'tags' => Tag::query()->whereHas('posts', fn (Builder $query) => $query->published())->orderBy('name')->get(),
            'recentPosts' => Post::query()->published()->latest('published_at')->limit(5)->get(),
        ]);
    }
}
