<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #f7f5fb; color: #302c42; font-family: system-ui, sans-serif; }
        a { color: inherit; text-decoration: none; }
        a:focus-visible { outline: 3px solid #8675bd; outline-offset: 5px; }
        .container { width: min(1180px, calc(100% - 3rem)); margin-inline: auto; }
        .site-header { background: #ffffffcc; border-bottom: 1px solid #e7e0f0; }
        .header-inner, .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; padding-block: 1.4rem; }
        .brand { font-size: 1.1rem; font-weight: 750; letter-spacing: -.03em; }
        .brand-mark { display: inline-grid; place-items: center; width: 2rem; height: 2rem; margin-right: .6rem; border-radius: .7rem; background: #e7ddfa; color: #66518c; }
        nav { display: flex; gap: .5rem; }
        nav a { padding: .6rem 1rem; border-radius: 999px; font-size: .9rem; }
        nav a:hover, nav a[aria-current="page"] { background: #eae3f7; color: #5b437c; }
        main { flex: 1; padding-block: 3.5rem; }
        .eyebrow { font-size: .75rem; font-weight: 750; letter-spacing: .16em; text-transform: uppercase; color: #796493; }
        h1 { font-size: clamp(2rem, 5vw, 3.5rem); letter-spacing: -.055em; margin: .7rem 0; }
        .intro { color: #746c82; line-height: 1.7; margin: 0; }
        .status { display: inline-flex; align-items: center; gap: .5rem; background: #e2f1e9; color: #3f6b56; border-radius: 999px; padding: .55rem .9rem; font-size: .8rem; margin-top: 1.5rem; }
        .panel { margin-top: 2rem; background: #fff; border: 1px solid #e5dfee; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 12px 40px #48345c08; }
        .panel-heading { padding: 1.2rem 1.5rem; background: #eee8f8; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .panel-heading h2 { margin: 0; font-size: 1rem; }
        .panel-heading span { font-size: .8rem; color: #756286; }
        iframe { display: block; width: 100%; height: 75vh; min-height: 28rem; border: 0; background: white; }
        .site-footer { border-top: 1px solid #e7e0f0; background: #f0ebf6; color: #796f85; font-size: .85rem; }
        .footer-inner p { margin: 0; }
        @media (max-width: 600px) { main { padding-block: 2rem; } .container { width: calc(100% - 2rem); } }
    </style>
    @stack('styles')
</head>
<body>
    @include('partials.header')
    <main id="main-content" class="container">
        @yield('content')
    </main>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
