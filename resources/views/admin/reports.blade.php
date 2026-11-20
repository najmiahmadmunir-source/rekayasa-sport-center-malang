@extends('layouts.admin')
@section('title', 'Laporan Transaksi')
@section('breadcrumb', 'Laporan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Laporan transaksi</h1>
    <p class="text-sm text-slate-500 mt-0.5">Rekap seluruh transaksi. Hanya pembayaran lunas dihitung sebagai pendapatan.</p>
</div>

<!-- Form filter periode laporan berdasarkan tanggal mulai dan tanggal akhir -->
<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-5 mb-6 flex flex-wrap items-center gap-4">
    <span class="text-xs font-semibold text-slate-600">Periode laporan</span>
    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
    <span class="text-xs text-slate-400">s/d</span>
    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
        Terapkan
    </button>
    <a href="{{ route('admin.reports') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Reset</a>
</form>

<!-- Kartu ringkasan Total Pendapatan Lunas dan Seluruh Transaksi -->
<div class="grid md:grid-cols-2 gap-5 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Total Pendapatan Lunas</div>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">Rp{{ number_format($paidRevenue, 0, ',', '.') }}</div>
        <div class="text-xs text-slate-500 mt-2">{{ $paidCount }} transaksi lunas • {{ $startDate }} s/d {{ $endDate }}</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Seluruh Transaksi</div>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $bookings->count() }} booking</div>
        <div class="text-xs text-slate-500 mt-2">{{ $unpaidCount }} transaksi belum lunas tidak masuk pendapatan</div>
    </div>
</div>

<!-- Tabel daftar seluruh transaksi -->
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h2 class="text-base font-bold text-slate-900 mb-4">Daftar seluruh transaksi</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="py-2.5 pr-4">Kode booking</th>
                    <th class="py-2.5 pr-4">Pelanggan</th>
                    <th class="py-2.5 pr-4">Lapangan / jadwal</th>
                    <th class="py-2.5 pr-4">Total biaya</th>
                    <th class="py-2.5 pr-4">Booking</th>
                    <th class="py-2.5 pr-4">Pembayaran</th>
                    <th class="py-2.5">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                <tr>
                    <td class="py-3 pr-4 font-semibold text-indigo-600">{{ $b->booking_code }}</td>
                    <td class="py-3 pr-4">{{ $b->user->name }}</td>
                    <td class="py-3 pr-4">
                        <div class="font-medium">{{ $b->court->name }}</div>
                        <div class="text-xs text-slate-400">{{ $b->booking_date }} • {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}</div>
                    </td>
                    <td class="py-3 pr-4 font-semibold">Rp{{ number_format($b->total_price, 0, ',', '.') }}</td>
                    <td class="py-3 pr-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            {{ ucfirst($b->status) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4">
                        @php $st = $b->payment->status ?? 'belum_bayar'; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $st === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $st === 'menunggu_cek' ? 'Menunggu cek' : ($st === 'terverifikasi' ? 'Terverifikasi' : ($st === 'lunas' ? 'Lunas' : 'Belum bayar')) }}
                        </span>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('admin.booking.detail', $b) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection