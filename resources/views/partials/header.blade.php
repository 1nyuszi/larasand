<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('index') }}"><span class="brand-mark" aria-hidden="true">S</span>{{ config('app.name') }}</a>
        <nav aria-label="Main navigation">
            <a href="{{ route('info') }}" @if(request()->routeIs('info')) aria-current="page" @endif>PHP info</a>
            <a href="{{ route('blog') }}" @if(request()->routeIs('blog', 'post', 'category', 'tag')) aria-current="page" @endif>Blog</a>
        </nav>
    </div>
</header>
