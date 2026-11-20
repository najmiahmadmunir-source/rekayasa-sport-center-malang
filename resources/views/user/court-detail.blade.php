@extends('layouts.user') {{-- Menggunakan layout utama portal pelanggan --}}
@section('title', 'Booking ' . $court->name) {{-- Judul halaman sesuai nama lapangan --}}

@section('content')
{{-- Header informasi lapangan --}}
<div class="mb-6">
    <a href="{{ route('user.dashboard') }}" class="text-xs font-semibold text-emerald-700 hover:underline">← Kembali ke katalog</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $court->name }} ({{ $court->code }})</h1>
    <p class="text-sm text-slate-500">
        Kategori: {{ $court->category->name }} •
        Tarif: <strong class="text-slate-900">Rp{{ number_format($court->price_per_hour, 0, ',', '.') }} / jam</strong> •
        Operasional: 07:00–22:00 WIB
    </p>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Pengecekan ketersediaan jadwal berdasarkan tanggal -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-3">Ketersediaan jadwal</h2>

        {{-- Form GET untuk memilih tanggal dan melihat slot jam yang sudah terbooking --}}
        <form method="GET" class="mb-4 flex gap-2">
            <input type="date" name="date" value="{{ $selectedDate }}" class="px-3 py-2 rounded-xl border border-slate-300 text-sm flex-1">
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold">Cek</button>
        </form>

        <div class="text-xs font-semibold text-slate-500 mb-2">Booking aktif pada {{ $selectedDate }}:</div>
        @if($existingBookings->isEmpty())
        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-medium">
            Belum ada booking aktif di tanggal ini. Slot 07:00–22:00 tersedia.
        </div>
        @else
        <ul class="space-y-2 text-xs">
            @foreach($existingBookings as $eb)
            <li class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex justify-between font-medium">
                <span>{{ substr($eb->start_time, 0, 5) }} – {{ substr($eb->end_time, 0, 5) }} WIB</span>
                <span>Terbooking</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    <!-- Kolom Kanan: Form pemesanan lapangan -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-base font-bold text-slate-900 mb-4">Form Pemesanan Lapangan</h2>

        {{-- Menampilkan pesan error validasi jika ada input yang belum sesuai --}}
        @if($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- enctype="multipart/form-data" wajib agar file bukti bayar QRIS terkirim ke server -->
        <form action="{{ route('user.booking.store', $court) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf {{-- Token keamanan CSRF --}}

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal main</label>
                    <input id="bookingDate" type="date" name="booking_date" value="{{ old('booking_date', $selectedDate) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam mulai (07:00–21:00)</label>
                    <select id="startHour" name="start_time" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        @for($h = 7; $h <= 21; $h++)
                            @php $val=sprintf('%02d:00', $h); @endphp
                            <option value="{{ $val }}" {{ old('start_time') === $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam selesai (08:00–22:00)</label>
                    <select id="endHour" name="end_time" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                        @for($h = 8; $h <= 22; $h++)
                            @php $val=sprintf('%02d:00', $h); @endphp
                            <option value="{{ $val }}" {{ old('end_time', '08:00') === $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endfor
                    </select>
                </div>
            </div>

            <!-- Panel pratinjau durasi dan total biaya otomatis (durasi x tarif per jam) -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs text-slate-500">Durasi pemesanan</div>
                    <div id="durationText" class="text-sm font-bold text-slate-900">1 jam</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500">Total biaya (Durasi × Tarif)</div>
                    <div id="totalPriceText" class="text-xl font-extrabold text-emerald-700">
                        Rp{{ number_format($court->price_per_hour, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Pilihan 2 metode pembayaran: QRIS atau Cash[cite: 3] -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Metode pembayaran</label>
                @php $selectedMethod = old('payment_method', 'qris'); @endphp
                <div class="grid sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-300 cursor-pointer hover:bg-slate-50">
                        <input type="radio" name="payment_method" value="qris" {{ $selectedMethod === 'qris' ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-bold text-slate-900">QRIS</div>
                            <div class="text-xs text-slate-500">Scan QR Code & wajib upload foto bukti payment</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-300 cursor-pointer hover:bg-slate-50">
                        <input type="radio" name="payment_method" value="cash" {{ $selectedMethod === 'cash' ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-bold text-slate-900">Cash</div>
                            <div class="text-xs text-slate-500">Bayar langsung di kasir, diverifikasi admin</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Bagian QRIS: tampilkan QR Code pembayaran dan wajibkan upload bukti bayar[cite: 3] -->
            <div id="qrisSection" class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200 space-y-4">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Jika punya file gambar QRIS lokal di public/images/qris.png, ganti src menjadi {{ asset('images/qris.png') }} --}}
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode('REKAYASA-SPORT-CENTER-MALANG-' . $court->code) }}"
                        alt="QR Code Pembayaran QRIS"
                        class="w-36 h-36 rounded-xl border border-slate-200 bg-white p-2 object-contain">
                    <div class="space-y-1 text-center sm:text-left">
                        <div class="text-sm font-bold text-slate-900">Scan QR Code untuk Pembayaran QRIS</div>
                        <p class="text-xs text-slate-600">
                            Bayar sesuai total biaya booking, lalu unggah foto bukti pembayaran di bawah ini.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Upload foto bukti pembayaran (Wajib untuk QRIS — JPG/PNG, maks. 2MB)
                    </label>
                    <input id="proofImageInput" type="file" name="proof_image" accept="image/jpeg,image/png,image/jpg"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                </div>
            </div>

            <!-- Bagian Cash: tidak perlu upload bukti bayar, di-update admin saat bayar di kasir[cite: 3] -->
            <div id="cashSection" class="hidden p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                <strong>Pembayaran Cash:</strong> Tidak perlu mengunggah bukti pembayaran. Silakan bayar secara langsung di kasir, lalu admin akan memperbarui status pembayaran setelah pembayaran diterima di kasir.
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition">
                Buat Booking & Dapatkan Kode
            </button>
        </form>
    </div>
</div>

<script>
    (function() {
        // Tarif per jam dari database untuk kalkulasi biaya real-time
        const PRICE_PER_HOUR = Number("{{ (int) $court->price_per_hour }}") || 0;

        // Mengambil angka jam dari nilai dropdown ("07:00" -> 7)
        function parseHourValue(val) {
            if (!val) return NaN;
            const match = String(val).match(/^(\d{1,2})/);
            return match ? parseInt(match[1], 10) : NaN;
        }

        // Memformat angka menjadi format Rupiah (contoh: 150000 -> Rp150.000)
        function formatIdr(value) {
            const safe = Math.max(0, Math.round(Number(value) || 0));
            return 'Rp' + safe.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Menghitung durasi (jam selesai - jam mulai) dan total biaya secara langsung
        function refreshBookingCalculation() {
            const startSelect = document.getElementById('startHour');
            const endSelect = document.getElementById('endHour');
            const durationEl = document.getElementById('durationText');
            const totalEl = document.getElementById('totalPriceText');

            if (!startSelect || !endSelect || !durationEl || !totalEl) return;

            const startHour = parseHourValue(startSelect.value);
            const endHour = parseHourValue(endSelect.value);

            if (Number.isNaN(startHour) || Number.isNaN(endHour)) return;

            const hours = endHour - startHour;
            if (hours > 0) {
                durationEl.textContent = hours + ' jam';
                totalEl.textContent = formatIdr(hours * PRICE_PER_HOUR);
            } else {
                durationEl.textContent = '0 jam';
                totalEl.textContent = formatIdr(0);
            }
        }

        // Mengatur tampilan QRIS vs Cash serta status wajib upload bukti bayar
        function togglePaymentMethodUI() {
            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            const method = checkedRadio ? checkedRadio.value : 'qris';

            const qrisSection = document.getElementById('qrisSection');
            const cashSection = document.getElementById('cashSection');
            const proofInput = document.getElementById('proofImageInput');

            if (method === 'cash') {
                if (qrisSection) qrisSection.classList.add('hidden');
                if (cashSection) cashSection.classList.remove('hidden');
                if (proofInput) {
                    proofInput.required = false;
                    proofInput.value = '';
                }
            } else {
                if (qrisSection) qrisSection.classList.remove('hidden');
                if (cashSection) cashSection.classList.add('hidden');
                if (proofInput) {
                    proofInput.required = true;
                }
            }
        }

        function bindEvents() {
            const startSelect = document.getElementById('startHour');
            const endSelect = document.getElementById('endHour');
            const methodRadios = document.querySelectorAll('input[name="payment_method"]');

            ['change', 'input', 'keyup', 'blur'].forEach(function(evtName) {
                if (startSelect) startSelect.addEventListener(evtName, refreshBookingCalculation);
                if (endSelect) endSelect.addEventListener(evtName, refreshBookingCalculation);
            });

            methodRadios.forEach(function(radio) {
                radio.addEventListener('change', togglePaymentMethodUI);
            });

            refreshBookingCalculation();
            togglePaymentMethodUI();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindEvents);
        } else {
            bindEvents();
        }
    })();
</script>
@endsection