<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Date & Time</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            background: #0f172a;
            color: #f8fafc;
            font-family: system-ui, sans-serif;
            text-align: center;
        }
        main { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 1.5rem; width: 100%; }
        .site-header { background: #ffffffcc; border-bottom: 1px solid #e7e0f0; color: #302c42; text-align: left; }
        .site-header .container { width: min(1180px, calc(100% - 3rem)); margin-inline: auto; }
        .header-inner { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; padding-block: 1.4rem; }
        .site-header a { color: inherit; text-decoration: none; }
        .site-header a:focus-visible { outline: 3px solid #8675bd; outline-offset: 5px; }
        .brand { font-size: 1.1rem; font-weight: 750; letter-spacing: -.03em; }
        .brand-mark { display: inline-grid; place-items: center; width: 2rem; height: 2rem; margin-right: .6rem; border-radius: .7rem; background: #e7ddfa; color: #66518c; }
        .site-header nav { display: flex; gap: .5rem; }
        .site-header nav a { padding: .6rem 1rem; border-radius: 999px; font-size: .9rem; }
        .site-header nav a:hover, .site-header nav a[aria-current="page"] { background: #eae3f7; color: #5b437c; }
        @media (max-width: 600px) { .site-header .container { width: calc(100% - 2rem); } }
        #time {
            display: block;
            font-size: clamp(3rem, 14vw, 12rem);
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            line-height: 1.15;
        }
        #date {
            margin: 1rem 0 0;
            color: #cbd5e1;
            font-size: clamp(1.5rem, 4vw, 3.5rem);
        }
    </style>
</head>
<body>
    @include('partials.header')
    <main aria-label="Current date and time">
        <time id="time">{{ now()->format('H:i:s') }}</time>
        <p id="date">{{ now()->format('l, F j, Y') }}</p>
        <noscript>Enable JavaScript for a live clock in your local time.</noscript>
    </main>
    <script>
        const time = document.getElementById('time');
        const date = document.getElementById('date');
        const timeFormatter = new Intl.DateTimeFormat(undefined, {
            hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23'
        });
        const dateFormatter = new Intl.DateTimeFormat(undefined, { dateStyle: 'full' });

        function updateClock() {
            const now = new Date();
            time.textContent = timeFormatter.format(now);
            time.dateTime = now.toISOString();
            date.textContent = dateFormatter.format(now);
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
