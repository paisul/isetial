<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','iSetial Wisdom')</title>
    <meta name="description" content="Pemuda-Pemudi 5 Masjid">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=20260919">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=20260919">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>[x-cloak]{display:none}.prose p{margin:.75rem 0}</style>
</head>
<body class="bg-slate-50 text-slate-800">
<nav class="sticky top-0 z-20 bg-emerald-950 text-white shadow">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl font-black tracking-tight">
            <img src="{{ asset('favicon.png') }}?v=20260919" alt="Logo iSetial Wisdom" width="42" height="42" class="h-[42px] w-[42px] rounded-full object-cover" loading="eager">
            <span>iSETIAL <span class="text-amber-400">WISDOM</span></span>
        </a>
        <button type="button" onclick="document.getElementById('nav').classList.toggle('hidden')" class="md:hidden" aria-label="Buka navigasi">☰</button>
        <div id="nav" class="hidden gap-5 text-sm md:flex">
            @if(isset($activeMasjid))
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.home':'portal.home',$activeMasjid) }}">Beranda</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.profile':'portal.profile',$activeMasjid) }}">Profil</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.dkm':'portal.dkm',$activeMasjid) }}">DKM</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.activities':'portal.activities',$activeMasjid) }}">Kegiatan</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.announcements':'portal.announcements',$activeMasjid) }}">Pengumuman</a>
            @else
                <a href="{{ route('about') }}">Tentang</a>
                <a href="{{ route('structure') }}">Pengurus</a>
                <a href="{{ route('masjids') }}">Masjid</a>
                <a href="{{ route('guidelines') }}">Pedoman</a>
                <a href="{{ route('activities') }}">Kegiatan</a>
                <a href="{{ route('jersey.create') }}">Jersey</a>
                <a href="{{ route('contact') }}">Kontak</a>
            @endif
            @auth
                <a href="{{ route('member.dashboard') }}">Portal Saya</a>
                <form method="post" action="{{route('logout')}}">@csrf<button>Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="font-bold text-amber-400">Login</a>
            @endauth
        </div>
    </div>
</nav>
@if(session('success'))
    <div class="mx-auto mt-4 max-w-5xl rounded-xl bg-emerald-100 px-4 py-3 text-emerald-900">{{session('success')}}</div>
@endif
@if($errors->any())
    <div class="mx-auto mt-4 max-w-5xl rounded-xl bg-red-100 px-4 py-3 text-red-900"><ul>@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
@endif
<main>@yield('content')</main>
<footer class="mt-16 bg-emerald-950 px-4 py-10 text-center text-sm text-emerald-100">© {{date('Y')}} iSetial Wisdom · Pemuda-Pemudi 5 Masjid</footer>
</body>
</html>
