@extends('layouts.app')

@section('title', ($category?->name ?? $tag?->name ?? 'Blog').' · '.config('app.name'))

@push('styles')
    @include('partials.blog-styles')
@endpush

@section('content')
    <div class="eyebrow">Stories & ideas</div>
    <h1>{{ $category?->name ?? ($tag ? '#'.$tag->name : 'The blog') }}</h1>
    <p class="intro">{{ $category?->description ?? 'Notes, discoveries, and things worth sharing.' }}</p>
    <div class="blog-layout">
        <div>
            <form class="blog-search" method="GET" action="{{ url()->current() }}" role="search">
                <label for="blog-search">Search articles</label>
                <div><input id="blog-search" name="q" type="search" maxlength="200" value="{{ $search }}" placeholder="What are you looking for?"><button type="submit">Search</button></div>
                @error('q') <p role="alert">{{ $message }}</p> @enderror
            </form>
            @if ($search !== '')
                <p class="intro">Results for “{{ $search }}” · <a href="{{ url()->current() }}">Clear search</a></p>
            @endif
            @if (!$category && !$tag && $search === '' && $featuredPosts->isNotEmpty())
                <section class="blog-card featured">
                    <div class="eyebrow">Featured</div>
                    @foreach ($featuredPosts as $featured)
                        <h2><a href="{{ route('post', $featured->slug) }}">{{ $featured->title }}</a></h2>
                    @endforeach
                </section>
            @endif
            <h2>{{ $category || $tag || $search !== '' ? 'Articles' : 'Latest articles' }}</h2>
            @include('partials.posts-list')
            @if ($posts->hasPages())
                <nav class="blog-pagination" aria-label="Article pages">
                    @if ($posts->onFirstPage()) <span>Previous</span> @else <a href="{{ $posts->previousPageUrl() }}" rel="prev">Previous</a> @endif
                    <span>Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
                    @if ($posts->hasMorePages()) <a href="{{ $posts->nextPageUrl() }}" rel="next">Next</a> @else <span>Next</span> @endif
                </nav>
            @endif
        </div>
        <aside class="blog-sidebar" aria-label="Browse the blog">
            <section class="blog-card">
                <h2>Categories</h2>
                <ul>
                    <li><a href="{{ route('blog') }}">All articles</a></li>
                    @foreach ($categories as $item)
                        <li><a href="{{ route('category', $item->slug) }}">{{ $item->name }}</a> <span>({{ $item->posts_count }})</span></li>
                    @endforeach
                </ul>
            </section>
            @if ($tags->isNotEmpty())
                <section class="blog-card">
                    <h2>Tags</h2>
                    <div class="blog-tags">@foreach ($tags as $item)<a href="{{ route('tag', $item->slug) }}">#{{ $item->name }}</a>@endforeach</div>
                </section>
            @endif
            @if ($recentPosts->isNotEmpty())
                <section class="blog-card">
                    <h2>Recent articles</h2>
                    <ul>@foreach ($recentPosts as $recent)<li><a href="{{ route('post', $recent->slug) }}">{{ $recent->title }}</a></li>@endforeach</ul>
                </section>
            @endif
        </aside>
    </div>
@endsection
