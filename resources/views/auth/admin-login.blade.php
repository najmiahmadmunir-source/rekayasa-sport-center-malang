<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Rekayasa Sport Center Malang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen grid lg:grid-cols-2 bg-slate-50">
    <!-- Panel kiri informasi portal admin -->
    <div class="bg-[#1e1b4b] text-white p-10 lg:p-14 flex flex-col justify-between">
        <div>
            <div class="text-xl font-extrabold tracking-tight">REKAYASA</div>
            <div class="text-xs tracking-widest text-indigo-300 uppercase mt-1">Sport Center Malang</div>
            <div class="mt-8 inline-block px-3 py-1 rounded-lg bg-indigo-500/30 text-indigo-200 text-xs font-semibold tracking-wider uppercase">
                Portal Admin
            </div>
        </div>

        <div class="my-10">
            <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight">
                Kelola lapangan.<br>Maksimalkan<br>potensi.
            </h1>
            <p class="text-indigo-200/80 mt-4 max-w-md text-sm leading-relaxed">
                Satu tempat untuk operasional, booking, verifikasi bukti pembayaran, dan laporan transaksi yang lebih tertata.
            </p>
            <!-- Ilustrasi lapangan dekoratif -->
            <div class="mt-8 rounded-2xl bg-indigo-900/60 border border-indigo-400/20 p-6 max-w-sm">
                <div class="grid grid-cols-3 border border-indigo-400/30 h-32">
                    <div class="border-r border-indigo-400/30"></div>
                    <div class="border-r border-indigo-400/30 relative flex items-center justify-center">
                        <span class="w-3 h-3 rounded-full bg-lime-400"></span>
                    </div>
                    <div></div>
                </div>
            </div>
        </div>

        <div class="text-xs text-indigo-300/80">
            <div class="uppercase tracking-wider text-[10px] text-indigo-400">Malang, Jawa Timur</div>
            <div class="mt-1 font-medium text-white">Buka setiap hari • 07:00–22:00 WIB</div>
        </div>
    </div>

    <!-- Panel kanan form login admin (tidak ada fitur registrasi untuk admin) -->
    <div class="flex flex-col justify-between p-8 lg:p-16">
        <div></div>
        <div class="max-w-md w-full mx-auto">
            <div class="text-xs font-bold tracking-wider text-indigo-600 uppercase">Akses Pengelola</div>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Masuk sebagai admin</h2>
            <p class="text-sm text-slate-500 mt-1">Gunakan akun admin yang sudah terdaftar.</p>

            <!-- Tampilkan pesan error jika kredensial salah -->
            @if($errors->any())
            <div class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <!-- Form dikirim ke route admin.login.post -->
            <form action="{{ route('admin.login.post') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email / username</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="admin@rekayasasport.id"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-sm focus:outline-none focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-sm focus:outline-none focus:border-indigo-600">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                    Login
                </button>
            </form>
        </div>
        <div class="text-center text-xs text-slate-400">© 2026 Rekayasa Sport Center Malang</div>
    </div>
</body>

</html>