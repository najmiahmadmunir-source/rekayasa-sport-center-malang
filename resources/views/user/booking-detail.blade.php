@extends('layouts.user')
@section('title', 'Detail Booking ' . $booking->booking_code)

@section('content')
@php
$payment = $booking->payment;
$rawPayStatus = $payment->status ?? 'belum_bayar';

$isRejected = \Illuminate\Support\Facades\Cache::get('rejected_payment_booking_' . $booking->id, false)
|| ($booking->status === 'ditolak')
|| ($rawPayStatus === 'ditolak')
|| (($booking->notes ?? '') === 'Ditolak oleh admin')
|| ((($payment->notes ?? '') === 'Ditolak oleh admin'));

if ($isRejected) {
$paymentStatusLabel = 'Ditolak';
$paymentStatusColor = 'text-rose-600';
} elseif ($rawPayStatus === 'lunas') {
$paymentStatusLabel = 'Lunas';
$paymentStatusColor = 'text-emerald-700';
} elseif ($rawPayStatus === 'terverifikasi') {
$paymentStatusLabel = 'Terverifikasi';
$paymentStatusColor = 'text-emerald-700';
} elseif ($rawPayStatus === 'menunggu_cek') {
$paymentStatusLabel = 'Menunggu cek';
$paymentStatusColor = 'text-amber-600';
} else {
$paymentStatusLabel = 'Belum bayar';
$paymentStatusColor = 'text-rose-600';
}

$bookingStatusLabel = ucfirst($booking->status ?? 'aktif');
@endphp

<div class="mb-4">
    <a href="{{ route('user.bookings') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
        ← Kembali ke daftar booking saya
    </a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-1">
        Kode Booking: {{ $booking->booking_code }}
    </h1>
</div>

@if (session('success'))
<div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
    {{ session('success') }}
</div>
@endif

@if ($isRejected)
<div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
    Pembayaran ini telah <strong>Ditolak</strong> oleh admin. Silakan hubungi admin atau lakukan konfirmasi ulang.
</div>
@endif

<div class="grid lg:grid-cols-2 gap-6">
    <!-- Kolom Kiri: Rincian Booking & Pembayaran -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-900">Rincian Booking &amp; Pembayaran</h2>

        <div class="divide-y divide-slate-100 text-sm">
            <div class="py-2.5 flex items-center justify-between gap-4">
                <span class="text-slate-500">Lapangan</span>
                <span class="font-semibold text-slate-900 text-right">
                    {{ $booking->court->name }} ({{ $booking->court->code }})
                </span>
            </div>

            <div class="py-2.5 flex items-center justify-between gap-4">
                <span class="text-slate-500">Tanggal</span>
                <span class="font-semibold text-slate-900 text-right">
                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}
                </span>
            </div>

            <div class="py-2.5 flex items-center justify-between gap-4">
                <span class="text-slate-500">Jam Main</span>
                <span class="font-semibold text-slate-900 text-right">
                    {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }} WIB ({{ $booking->duration_hours }} jam)
                </span>
            </div>

            <div class="py-2.5 flex items-center justify-between gap-4">
                <span class="text-slate-500">Status Booking</span>
                <span class="font-semibold text-slate-900 text-right">
                    {{ $bookingStatusLabel }}
                </span>
            </div>

            <div class="py-2.5 flex items-center justify-between gap-4">
                <span class="text-slate-500">Status Pembayaran</span>
                <span class="font-bold {{ $paymentStatusColor }} text-right">
                    {{ $paymentStatusLabel }}
                </span>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-4">
            <span class="text-sm font-bold text-slate-800">Total Biaya</span>
            <span class="text-2xl font-extrabold text-slate-900">
                Rp{{ number_format($booking->total_price, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Kolom Kanan: Bukti Pembayaran Gambar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900">Bukti Pembayaran Gambar</h2>
        <p class="text-xs text-slate-500 mt-1 mb-4">
            Bukti pembayaran tercatat saat pembuatan booking untuk diperiksa admin.
        </p>

        @if (!empty($payment?->proof_image))
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center">
            <img
                src="{{ asset('storage/' . $payment->proof_image) }}"
                alt="Bukti Pembayaran {{ $booking->booking_code }}"
                class="max-h-72 rounded-lg object-contain">
        </div>
        @else
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 leading-relaxed">
            Tidak ada bukti gambar (metode pembayaran <strong>Cash</strong>). Silakan bayar langsung di kasir agar admin dapat memperbarui status pembayaran.
        </div>
        @endif
    </div>
</div>
@endsection