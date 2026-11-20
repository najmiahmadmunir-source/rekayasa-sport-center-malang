@extends('layouts.admin') <!-- Menggunakan layout utama portal admin -->
@section('title', 'Dashboard Admin')
@section('breadcrumb', 'Dashboard')

@section('content')
<!-- Judul halaman dan tanggal hari ini -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Ringkasan operasional</h1>
        <p class="text-sm text-slate-500 mt-0.5">Selamat datang kembali. Pantau aktivitas sport center hari ini.</p>
    </div>
    <span class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700">
        {{ now()->translatedFormat('d F Y') }}
    </span>
</div>

<!-- 3 Kartu KPI: Total Pendapatan Lunas, Jumlah Booking, dan Lapangan Aktif -->
<div class="grid md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 border-l-4 border-l-indigo-600">
        <div class="text-xs font-medium text-slate-500">Total Pendapatan Lunas</div>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div> <!-- Format rupiah -->
        <div class="text-xs font-semibold text-indigo-600 mt-2">{{ $paidTransactions }} transaksi lunas</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 border-l-4 border-l-sky-500">
        <div class="text-xs font-medium text-slate-500">Jumlah Booking</div>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalBookings }}</div>
        <div class="text-xs font-semibold text-sky-600 mt-2">Total seluruh pemesanan</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 border-l-4 border-l-emerald-500">
        <div class="text-xs font-medium text-slate-500">Lapangan Aktif</div>
        <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $activeCourts }} / {{ $totalCourts }}</div>
        <div class="text-xs font-semibold text-emerald-600 mt-2">{{ $totalCategories }} kategori olahraga</div>
    </div>
</div>

<!-- Pintasan Operasional & Kartu Notifikasi Bukti Pembayaran Menunggu Verifikasi -->
<div class="grid lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Status Operasional Lapangan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Jam operasional aktif setiap hari pukul 07:00–22:00 WIB.</p>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('admin.courts') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                Kelola Kategori & Lapangan
            </a>
            <a href="{{ route('admin.reports') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                Lihat Laporan Transaksi
            </a>
        </div>
    </div>

    <div class="bg-indigo-50/70 rounded-2xl border border-indigo-100 p-6 flex flex-col justify-between">
        <div>
            <span class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-700 text-[10px] font-bold uppercase">Perlu Tindakan</span>
            <h2 class="text-xl font-extrabold text-slate-900 mt-3">{{ $pendingVerifications }} bukti pembayaran menunggu verifikasi</h2>
            <p class="text-xs text-slate-600 mt-1.5">Periksa bukti transfer gambar sebelum mengubah status pembayaran menjadi lunas.</p>
        </div>
        <a href="{{ route('admin.bookings', ['payment_status' => 'menunggu_cek']) }}"
            class="mt-5 inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">
            Tinjau pembayaran →
        </a>
    </div>
</div>

<!-- Tabel Booking Terbaru -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-900">Booking terbaru</h2>
        <a href="{{ route('admin.bookings') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="py-2.5 pr-4">Kode booking</th>
                    <th class="py-2.5 pr-4">Pelanggan / lapangan</th>
                    <th class="py-2.5 pr-4">Jadwal</th>
                    <th class="py-2.5 pr-4">Total biaya</th>
                    <th class="py-2.5 pr-4">Pembayaran</th>
                    <th class="py-2.5">Bukti bayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentBookings as $b)
                <tr>
                    <td class="py-3 pr-4 font-semibold text-indigo-600">
                        <a href="{{ route('admin.booking.detail', $b) }}">{{ $b->booking_code }}</a>
                    </td>
                    <td class="py-3 pr-4">{{ $b->user->name }} • {{ $b->court->name }}</td>
                    <td class="py-3 pr-4 text-slate-600">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M') }}, {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}</td>
                    <td class="py-3 pr-4 font-semibold">Rp{{ number_format($b->total_price, 0, ',', '.') }}</td>
                    <td class="py-3 pr-4">
                        @php $st = $b->payment->status ?? 'belum_bayar'; @endphp <!-- Ambil status pembayaran -->
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $st === 'lunas' ? 'bg-emerald-50 text-emerald-700' : ($st === 'menunggu_cek' || $st === 'terverifikasi' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                            {{ str_replace('_', ' ', ucfirst($st)) }}
                        </span>
                    </td>
                    <td class="py-3 text-xs text-slate-500">
                        {{ optional($b->payment)->proof_image ? 'Sudah diunggah' : 'Belum ada' }} <!-- Cek apakah gambar bukti bayar sudah diunggah -->
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400">Belum ada data booking.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection