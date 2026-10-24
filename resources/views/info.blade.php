@extends('layouts.app')

@section('title', 'PHP information')

@section('content')
    <div class="eyebrow">Under the hood</div>
    <h1>PHP information</h1>
    <p class="intro">A look at the runtime, configuration, and extensions powering this application.</p>
    <div class="status">PHP {{ PHP_VERSION }}</div>

    <section class="panel" aria-labelledby="runtime-heading">
        <div class="panel-heading">
            <h2 id="runtime-heading">Runtime overview</h2>
            <span>Configuration &amp; loaded modules</span>
        </div>
        <iframe title="PHP runtime configuration and modules" sandbox srcdoc="{{ str_replace('</head>', '<style>body{background:#fff;color:#302c42;font-family:system-ui,sans-serif;padding:16px;margin:0}body .center{width:100%;text-align:left}table{width:100%;max-width:100%;border-collapse:collapse;margin:0 0 20px;table-layout:fixed}td,th{border:1px solid #e5dfee;padding:10px;overflow-wrap:anywhere;font-size:13px}.h{background:#eee8f8}.e{background:#f3eef9;color:#59476f}.v{background:#fbfafc;color:#51485e}h1{font-size:24px}h2{font-size:18px}pre{white-space:pre-wrap;overflow-wrap:anywhere}a{color:#796493}</style></head>', $phpInfo) }}"></iframe>
    </section>
@endsection
