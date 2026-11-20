@extends('layouts.admin')
@section('title', 'Booking & Pembayaran')
@section('breadcrumb', 'Booking & Pembayaran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Booking & pembayaran</h1>
    <p class="text-sm text-slate-500 mt-0.5">Tinjau jadwal booking dan verifikasi bukti pembayaran pelanggan.</p>
</div>

<!-- Tab filter cepat berdasarkan status pembayaran -->
<div class="bg-white rounded-2xl border border-slate-200 p-2 mb-5 flex flex-wrap gap-2 text-xs font-semibold">
    <a href="{{ route('admin.bookings') }}"
        class="px-4 py-2 rounded-xl {{ !request('payment_status') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}">
        Semua booking <span class="ml-1 text-slate-400">{{ $counts['all'] }}</span>
    </a>
    <a href="{{ route('admin.bookings', ['payment_status' => 'menunggu_cek']) }}"
        class="px-4 py-2 rounded-xl {{ request('payment_status') === 'menunggu_cek' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}">
        Menunggu cek <span class="ml-1 text-slate-400">{{ $counts['menunggu_cek'] }}</span>
    </a>
    <a href="{{ route('admin.bookings', ['payment_status' => 'lunas']) }}"
        class="px-4 py-2 rounded-xl {{ request('payment_status') === 'lunas' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}">
        Lunas <span class="ml-1 text-slate-400">{{ $counts['lunas'] }}</span>
    </a>
    <a href="{{ route('admin.bookings', ['payment_status' => 'belum_bayar']) }}"
        class="px-4 py-2 rounded-xl {{ request('payment_status') === 'belum_bayar' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}">
        Belum bayar <span class="ml-1 text-slate-400">{{ $counts['belum_bayar'] }}</span>
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <!-- Form filter pencarian kode/nama, tanggal, dan lapangan -->
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        @if(request('payment_status'))
        <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode booking / nama..."
            class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm w-60">
        <input type="date" name="date" value="{{ request('date') }}"
            class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
        <select name="court_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            <option value="">Semua lapangan</option>
            @foreach($allCourts as $c)
            <option value="{{ $c->id }}" {{ request('court_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold">Terapkan</button>
        <a href="{{ route('admin.bookings') }}" class="text-xs font-semibold text-indigo-600 px-2">Reset</a>
    </form>

    <!-- Tabel daftar booking & pembayaran -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="py-2.5 pr-4">Kode booking</th>
                    <th class="py-2.5 pr-4">Pelanggan / lapangan</th>
                    <th class="py-2.5 pr-4">Jadwal</th>
                    <th class="py-2.5 pr-4">Durasi</th>
                    <th class="py-2.5 pr-4">Total biaya</th>
                    <th class="py-2.5 pr-4">Pembayaran</th>
                    <th class="py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                <tr>
                    <td class="py-3.5 pr-4 font-semibold text-indigo-600">{{ $b->booking_code }}</td>
                    <td class="py-3.5 pr-4">
                        <div class="font-semibold text-slate-900">{{ $b->user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $b->court->name }}</div>
                    </td>
                    <td class="py-3.5 pr-4">
                        <div>{{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</div>
                        <div class="text-xs text-slate-400">{{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }} WIB</div>
                    </td>
                    <td class="py-3.5 pr-4">{{ $b->duration_hours }} jam</td>
                    <td class="py-3.5 pr-4 font-bold">Rp{{ number_format($b->total_price, 0, ',', '.') }}</td>
                    <td class="py-3.5 pr-4">
                        @php $st = $b->payment->status ?? 'belum_bayar'; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $st === 'lunas' ? 'bg-emerald-50 text-emerald-700' : ($st === 'menunggu_cek' || $st === 'terverifikasi' ? 'bg-amber-50 text-amber-700' : 'bg-amber-50/70 text-amber-600') }}">
                            {{ $st === 'menunggu_cek' ? 'Menunggu cek' : ($st === 'terverifikasi' ? 'Terverifikasi' : ($st === 'lunas' ? 'Lunas' : 'Belum bayar')) }}
                        </span>
                    </td>
                    <td class="py-3.5">
                        <!-- Tombol menuju halaman detail untuk melihat bukti bayar & verifikasi -->
                        <a href="{{ route('admin.booking.detail', $b) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            Detail →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data booking.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection