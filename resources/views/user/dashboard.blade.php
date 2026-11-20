@extends('layouts.user')
@section('title', 'Katalog Lapangan')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Katalog & jadwal lapangan</h1>
        <p class="text-sm text-slate-500 mt-0.5">Pilih kategori olahraga dan lapangan aktif (Jam operasional 07:00–22:00 WIB).</p>
    </div>
    <!-- Pintasan ke riwayat booking milik pelanggan sendiri -->
    <a href="{{ route('user.bookings') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
        Booking Saya ({{ $myBookingsCount }})
    </a>
</div>

<!-- Form pencarian nama/kode lapangan dan filter kategori aktif -->
<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 flex flex-wrap items-center gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode lapangan..."
        class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm flex-1 min-w-[200px]">
    <select name="category_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
        Cari
    </button>
    <a href="{{ route('user.dashboard') }}" class="text-xs font-semibold text-slate-500 px-2">Reset</a>
</form>

<!-- Grid kartu lapangan aktif -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
    @forelse($courts as $court)
    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between text-xs">
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold">{{ $court->category->name }}</span>
                <span class="font-mono text-slate-400">{{ $court->code }}</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-3">{{ $court->name }}</h2>
            <div class="text-sm font-extrabold text-emerald-700 mt-1">
                Rp{{ number_format($court->price_per_hour, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">/ jam</span>
            </div>
        </div>
        <!-- Tombol menuju halaman detail lapangan untuk cek jadwal & booking -->
        <a href="{{ route('user.court.detail', $court) }}"
            class="mt-5 w-full py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-center text-xs font-semibold transition">
            Cek Jadwal & Booking
        </a>
    </div>
    @empty
    <div class="col-span-4 bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
        Tidak ada lapangan aktif yang sesuai pencarian.
    </div>
    @endforelse
</div>
@endsection