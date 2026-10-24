<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('index') }}"><span class="brand-mark" aria-hidden="true">S</span>{{ config('app.name') }}</a>
        <nav aria-label="Main navigation">
            <a href="{{ route('index') }}">Home</a>
            <a href="{{ route('info') }}" @if(request()->routeIs('info')) aria-current="page" @endif>PHP info</a>
        </nav>
    </div>
</header>
