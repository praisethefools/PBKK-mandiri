<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title Dinamis --}}
    <title>PBKK ITS — @yield('title', 'Portal Akademik')</title>

    {{-- Asset Bundler Vite Lokal (NPM) tanpa CDN mentah --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $isDark = ($mode ?? request()->query('mode')) === 'dark';
@endphp
<body class="min-h-screen flex flex-col font-sans transition-colors duration-200 {{ $isDark ? 'bg-slate-900 text-slate-100' : 'bg-slate-50 text-slate-900' }}">

    {{-- 1. Menyisipkan bagian navbar --}}
    @include('partials.navbar')

    {{-- 2. Tempat konten utama halaman anak --}}
    <main class="container mx-auto px-4 sm:px-6 py-8 flex-1">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- 3. Menyisipkan bagian footer ITS --}}
    @include('partials.footer')

</body>
</html>
