@extends('layouts.app')

@section('title', $post->title.' · '.config('app.name'))

@push('styles')
    @include('partials.blog-styles')
@endpush

@section('content')
    <a class="blog-read-more" href="{{ route('blog') }}">← All articles</a>
    <article class="blog-article">
        <div class="eyebrow"><a href="{{ route('category', $post->category->slug) }}">{{ $post->category->name }}</a></div>
        <h1>{{ $post->title }}</h1>
        <p class="blog-meta">{{ $post->user->name }} · <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('F j, Y') }}</time></p>
        @if ($post->excerpt)<p class="intro">{{ $post->excerpt }}</p>@endif
        @if ($post->featured_image)<img class="blog-image" src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="">@endif
        <div class="blog-content">{{ $post->content }}</div>
        <div class="blog-tags">@foreach ($post->tags as $tag)<a href="{{ route('tag', $tag->slug) }}">#{{ $tag->name }}</a>@endforeach</div>
    </article>
@endsection
