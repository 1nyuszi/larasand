@forelse ($posts as $post)
    <article class="blog-card">
        @if ($post->featured_image)
            <img class="blog-image" src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="" loading="lazy">
        @endif
        <h2><a href="{{ route('post', $post->slug) }}">{{ $post->title }}</a></h2>
        <p class="blog-meta">
            {{ $post->user->name }} ·
            <a href="{{ route('category', $post->category->slug) }}">{{ $post->category->name }}</a> ·
            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('F j, Y') }}</time>
        </p>
        <p class="blog-excerpt">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 160) }}</p>
        <div class="blog-tags">@foreach ($post->tags as $item)<a href="{{ route('tag', $item->slug) }}">#{{ $item->name }}</a>@endforeach</div>
        <a class="blog-read-more" href="{{ route('post', $post->slug) }}">Read article →</a>
    </article>
@empty
    <div class="blog-card"><p>No articles found. Check back soon or try another search.</p></div>
@endforelse
