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
            display: grid;
            place-items: center;
            background: #0f172a;
            color: #f8fafc;
            font-family: system-ui, sans-serif;
            text-align: center;
        }
        main { padding: 1.5rem; width: 100%; }
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
