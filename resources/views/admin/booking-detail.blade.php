@extends('layouts.admin') {{-- Layout utama portal admin --}}
@section('title', 'Detail Booking ' . $booking->booking_code) {{-- Judul halaman dinamis --}}

@section('content')
@php
$payment = $booking->payment;
$payStatus = $payment->status ?? 'belum_bayar';

$payLabel = match ($payStatus) {
'menunggu_cek' => 'Menunggu Cek Admin',
'terverifikasi' => 'Terverifikasi',
'lunas' => 'Lunas',
default => 'Belum Bayar',
};

$payBadgeClass = match ($payStatus) {
'menunggu_cek' => 'bg-amber-100 text-amber-800',
'terverifikasi', 'lunas' => 'bg-emerald-100 text-emerald-800',
default => 'bg-rose-100 text-rose-800',
};

$bookingBadgeText = match ($booking->status ?? 'aktif') {
'selesai' => 'Booking selesai',
'dibatalkan' => 'Booking dibatalkan',
default => 'Booking aktif',
};
@endphp

<div class="mb-5">
    <a href="{{ route('admin.bookings') }}" class="text-xs font-semibold text-indigo-700 hover:underline">
        ← Booking &amp; pembayaran
    </a>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-1">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Detail booking</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $booking->booking_code }} • Dibuat {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>
        <span class="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
            {{ $bookingBadgeText }}
        </span>
    </div>
</div>

@if (session('success'))
<div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
    {{ $errors->first() }}
</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Informasi Booking & Status Pembayaran -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-900 mb-4">Informasi booking</h2>

            <div class="divide-y divide-slate-100 text-sm">
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Kode booking</span>
                    <span class="font-bold text-slate-900">{{ $booking->booking_code }}</span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Pelanggan</span>
                    <span class="font-semibold text-slate-900">{{ $booking->user->name ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Email / HP</span>
                    <span class="font-medium text-slate-700 text-right">
                        {{ $booking->user->email ?? '-' }} • {{ $booking->user->phone ?? '-' }}
                    </span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Lapangan</span>
                    <span class="font-semibold text-slate-900 text-right">
                        {{ $booking->court->code }} • {{ $booking->court->name }} ({{ $booking->court->category->name ?? '-' }})
                    </span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Tanggal</span>
                    <span class="font-semibold text-slate-900">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Jadwal</span>
                    <span class="font-semibold text-slate-900">
                        {{ substr($booking->start_time, 0, 5) }}–{{ substr($booking->end_time, 0, 5) }} WIB
                    </span>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <span class="text-slate-500">Durasi &amp; tarif</span>
                    <span class="font-medium text-slate-700">
                        {{ $booking->duration_hours }} jam × Rp{{ number_format($booking->court->price_per_hour ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="pt-4 mt-2 border-t border-slate-200 flex items-center justify-between">
                <span class="text-sm font-bold text-slate-800">Total biaya</span>
                <span class="text-2xl font-extrabold text-indigo-700">
                    Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between gap-4 mb-3">
                <h2 class="text-base font-bold text-slate-900">Status pembayaran</h2>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $payBadgeClass }}">
                    {{ $payLabel }}
                </span>
            </div>

            <ol class="text-xs text-slate-500 space-y-1 list-decimal list-inside mb-4">
                <li>Cocokkan nominal, pengirim, dan bukti transfer gambar (atau pembayaran langsung di kasir untuk Cash).</li>
                <li>Verifikasi bukti pembayaran terlebih dahulu sebelum mengubah status menjadi lunas, atau tekan <strong>Tolak</strong> jika bukti terindikasi palsu/tidak valid.</li>
            </ol>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Tombol Verifikasi Pembayaran --}}
                <form action="{{ route('admin.booking.verify', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                        Verifikasi Pembayaran
                    </button>
                </form>

                {{-- Tombol Tolak di sebelah Verifikasi Pembayaran --}}
                <form action="{{ route('admin.booking.reject', $booking) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menolak pembayaran ini karena bukti tidak valid atau terindikasi palsu?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition">
                        Tolak
                    </button>
                </form>

                {{-- Tombol Ubah Jadi Lunas --}}
                <form action="{{ route('admin.booking.paid', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Ubah Jadi Lunas
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Bukti Pembayaran -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Bukti pembayaran</h2>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">
                @if (!empty($payment?->uploaded_at))
                Diunggah pelanggan • {{ \Carbon\Carbon::parse($payment->uploaded_at)->translatedFormat('d M Y, H:i') }} WIB
                @else
                Tidak ada file bukti pembayaran yang diunggah
                @endif
            </p>

            @if (!empty($payment?->proof_image))
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center">
                <img
                    src="{{ asset('storage/' . $payment->proof_image) }}"
                    alt="Bukti Pembayaran {{ $booking->booking_code }}"
                    class="max-h-72 rounded-lg object-contain">
            </div>
            @else
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                Pembayaran Cash / belum ada bukti gambar terunggah. Pastikan pembayaran sudah diterima di kasir sebelum mengubah status.
            </div>
            @endif
        </div>

        @if (!empty($payment?->proof_image))
        <div class="mt-4">
            <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center justify-center gap-2 transition">
                <span>Lihat ukuran penuh</span>
                <span>↗</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection