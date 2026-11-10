<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Responden') · SyTIK PNL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sytik.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app"><p class="boot">Memuat…</p></div>

    {{-- Penampil error: kalau ada masalah JavaScript, pesannya tampil di layar --}}
    <script>
        window.__err = function (m) {
            var a = document.getElementById('app');
            a.innerHTML = '<pre style="white-space:pre-wrap;padding:24px;color:#b33d57;font:14px monospace">⚠ ' + String(m).replace(/</g, '&lt;') + '</pre>';
        };
        addEventListener('error', function (e) { __err(e.message + ' (' + String(e.filename || '').split('/').pop() + ':' + e.lineno + ')'); });
        addEventListener('unhandledrejection', function (e) { __err(e.reason); });
    </script>

    {{-- Data dari Laravel untuk React --}}
    <script>window.__DATA__ = @json($data ?? new stdClass);</script>

    {{-- React tanpa build step (tanpa JSX): React + htm lewat CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/react@18.3.1/umd/react.production.min.js" onerror="__err('Gagal memuat React dari CDN. Cek internet atau matikan Brave Shields untuk situs ini.')"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-dom@18.3.1/umd/react-dom.production.min.js" onerror="__err('Gagal memuat React DOM dari CDN.')"></script>
    <script src="https://cdn.jsdelivr.net/npm/htm@3.1.1/dist/htm.umd.js" onerror="__err('Gagal memuat htm dari CDN.')"></script>

    @include('partials.ui')
    @yield('script')
</body>
</html>
