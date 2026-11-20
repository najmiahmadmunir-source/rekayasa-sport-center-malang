<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk & Daftar — Rekayasa Sport Center Malang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden grid md:grid-cols-2">
        {{-- Kolom Kiri: Branding Rekayasa Sport Center Malang --}}
        <div class="bg-emerald-800 text-white p-8 md:p-10 flex flex-col justify-between">
            <div>
                <div class="text-sm font-extrabold tracking-wide uppercase">REKAYASA</div>
                <div class="text-xs text-emerald-200 tracking-wider uppercase">SPORT CENTER MALANG</div>

                <h1 class="text-2xl md:text-3xl font-extrabold leading-snug mt-8">
                    Booking lapangan olahraga jadi lebih mudah.
                </h1>
                <p class="text-sm text-emerald-100 mt-4 leading-relaxed">
                    Cek jadwal tersedia, pilih jam main (07:00–22:00 WIB), dapatkan kode booking, dan lakukan pembayaran dengan cepat.
                </p>
            </div>

            <div class="text-xs text-emerald-200 mt-10">
                Buka setiap hari • 07:00–22:00 WIB
            </div>
        </div>

        {{-- Kolom Kanan: 2 Option Tab Switcher (Masuk Pelanggan vs Daftar Akun Baru) --}}
        <div class="p-8 md:p-10 flex flex-col justify-center">

            {{-- Tombol 2 Option (Tab Switcher) --}}
            <div class="grid grid-cols-2 p-1 rounded-xl bg-slate-100 border border-slate-200 mb-6">
                <button type="button" id="tabLoginBtn" onclick="switchAuthTab('login')"
                    class="py-2.5 text-xs font-bold rounded-lg bg-white text-emerald-700 shadow-sm transition">
                    Masuk Pelanggan
                </button>
                <button type="button" id="tabRegisterBtn" onclick="switchAuthTab('register')"
                    class="py-2.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition">
                    Daftar Akun Baru
                </button>
            </div>

            {{-- Notifikasi sukses setelah registrasi berhasil -> diarahkan ke login --}}
            @if (session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                {{ session('success') }}
            </div>
            @endif

            {{-- Notifikasi error validasi --}}
            @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- FORM 1: MASUK PELANGGAN --}}
            <div id="panelLogin">
                <h2 class="text-xl font-extrabold text-slate-900">Masuk Pelanggan</h2>
                <p class="text-xs text-slate-500 mt-1 mb-5">Sudah punya akun? Masuk untuk melihat jadwal & booking.</p>

                <form action="{{ route('user.login.post') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Password" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm transition">
                        Masuk
                    </button>
                </form>

                <p class="text-xs text-slate-500 text-center mt-5">
                    Belum punya akun?
                    <button type="button" onclick="switchAuthTab('register')" class="font-bold text-emerald-700 hover:underline">
                        Daftar di sini
                    </button>
                </p>
            </div>

            {{-- FORM 2: DAFTAR AKUN BARU --}}
            <div id="panelRegister" class="hidden">
                <h2 class="text-xl font-extrabold text-slate-900">Daftar Akun Baru</h2>
                <p class="text-xs text-slate-500 mt-1 mb-5">Khusus pendaftaran pelanggan baru. Setelah daftar, silakan login.</p>

                <form action="{{ route('user.register.post') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>
                    <div>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="No. HP (contoh: 08123456789)" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="password" name="password" placeholder="Password (min 6)" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                        <input type="password" name="password_confirmation" placeholder="Ulangi Password" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition">
                        Daftar Pelanggan
                    </button>
                </form>

                <p class="text-xs text-slate-500 text-center mt-5">
                    Sudah punya akun?
                    <button type="button" onclick="switchAuthTab('login')" class="font-bold text-emerald-700 hover:underline">
                        Masuk sekarang
                    </button>
                </p>
            </div>

        </div>
    </div>

    @php
    $shouldOpenRegister = $errors->any() && (old('name') !== null || old('phone') !== null);
    @endphp

    <script>
        function switchAuthTab(tab) {
            const panelLogin = document.getElementById('panelLogin');
            const panelRegister = document.getElementById('panelRegister');
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');

            if (tab === 'register') {
                panelLogin.classList.add('hidden');
                panelRegister.classList.remove('hidden');

                tabRegisterBtn.className = 'py-2.5 text-xs font-bold rounded-lg bg-white text-emerald-700 shadow-sm transition';
                tabLoginBtn.className = 'py-2.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition';
            } else {
                panelRegister.classList.add('hidden');
                panelLogin.classList.remove('hidden');

                tabLoginBtn.className = 'py-2.5 text-xs font-bold rounded-lg bg-white text-emerald-700 shadow-sm transition';
                tabRegisterBtn.className = 'py-2.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const openRegister = @json($shouldOpenRegister);
            if (openRegister) {
                switchAuthTab('register');
            }
        });
    </script>
</body>

</html>