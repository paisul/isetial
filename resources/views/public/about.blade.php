@extends('layouts.app')
@section('title','Tentang Kami')
@section('content')
<div class="mx-auto max-w-5xl px-4 py-16">
    <h1 class="text-4xl font-black">Tentang iSetial Wisdom</h1>
    <p class="mt-5 text-lg">Wadah kolaborasi Pemuda-Pemudi dari lima masjid yang membangun generasi berilmu, berakhlak, dan bermanfaat.</p>
    <div class="mt-10 grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl bg-emerald-900 p-7 text-white">
            <h2 class="text-2xl font-bold">Visi</h2>
            <p class="mt-3">Menjadi gerakan pemuda masjid yang solid, bertumbuh, dan memberi dampak.</p>
        </div>
        <div class="rounded-2xl bg-white p-7 shadow">
            <h2 class="text-2xl font-bold">Misi</h2>
            <p class="mt-3">Menguatkan ukhuwah, mengembangkan kapasitas anggota, dan menyelenggarakan kegiatan sosial-keagamaan.</p>
        </div>
    </div>
</div>
@endsection
