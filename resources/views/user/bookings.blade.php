@extends('layouts.user')
@section('title', 'Booking & Pembayaran Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Booking &amp; pembayaran saya</h1>
    <p class="text-sm text-slate-500 mt-0.5">Menampilkan daftar booking dan pembayaran milik akun Anda sendiri.</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                <tr>
                    <th class="py-3.5 px-5 font-semibold">Kode booking</th>
                    <th class="py-3.5 px-5 font-semibold">Lapangan</th>
                    <th class="py-3.5 px-5 font-semibold">Jadwal</th>
                    <th class="py-3.5 px-5 font-semibold">Total biaya</th>
                    <th class="py-3.5 px-5 font-semibold">Status booking</th>
                    <th class="py-3.5 px-5 font-semibold">Pembayaran</th>
                    <th class="py-3.5 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                @php
                $rawPayStatus = $b->payment->status ?? 'belum_bayar';
                $isRejected = \Illuminate\Support\Facades\Cache::get('rejected_payment_booking_' . $b->id, false)
                || ($b->status === 'ditolak')
                || ($rawPayStatus === 'ditolak')
                || (($b->notes ?? '') === 'Ditolak oleh admin')
                || ((($b->payment->notes ?? '') === 'Ditolak oleh admin'));

                if ($isRejected) {
                $payText = 'Ditolak';
                $payBadge = 'bg-rose-100 text-rose-700';
                } elseif ($rawPayStatus === 'lunas') {
                $payText = 'Lunas';
                $payBadge = 'bg-emerald-100 text-emerald-700';
                } elseif ($rawPayStatus === 'terverifikasi') {
                $payText = 'Terverifikasi';
                $payBadge = 'bg-emerald-100 text-emerald-700';
                } elseif ($rawPayStatus === 'menunggu_cek') {
                $payText = 'Menunggu Cek';
                $payBadge = 'bg-amber-100 text-amber-800';
                } else {
                $payText = 'Belum Bayar';
                $payBadge = 'bg-rose-100 text-rose-700';
                }
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-5 font-extrabold text-emerald-700">{{ $b->booking_code }}</td>
                    <td class="py-3.5 px-5 font-semibold text-slate-900">
                        {{ $b->court->name }} ({{ $b->court->category->name ?? '-' }})
                    </td>
                    <td class="py-3.5 px-5 text-slate-700">
                        {{ \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d') }} •
                        {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}
                    </td>
                    <td class="py-3.5 px-5 font-bold text-slate-900">
                        Rp{{ number_format($b->total_price, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-5 text-slate-700 font-medium">
                        {{ ucfirst($b->status ?? 'aktif') }}
                    </td>
                    <td class="py-3.5 px-5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $payBadge }}">
                            {{ $payText }}
                        </span>
                    </td>
                    <td class="py-3.5 px-5 text-right">
                        <a href="{{ route('user.booking.detail', $b) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                            Detail &amp; Bukti →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 px-5 text-center text-sm text-slate-500">
                        Belum ada data booking.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection