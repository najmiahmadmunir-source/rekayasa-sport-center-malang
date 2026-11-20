<!DOCTYPE html> <!-- Mendeklarasikan dokumen HTML5 -->
<html lang="id"> <!-- Mengatur bahasa halaman ke Bahasa Indonesia -->

<head>
    <meta charset="UTF-8"> <!-- Encoding karakter UTF-8 agar simbol dan huruf tampil benar -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif di berbagai ukuran layar -->
    <title>@yield('title', 'Portal Admin') — Rekayasa Sport Center Malang</title> <!-- Judul tab dinamis per halaman -->
    <script src="https://cdn.tailwindcss.com"></script> <!-- Memuat Tailwind CSS via CDN tanpa perlu build lokal -->
</head>

<body class="bg-slate-50 text-slate-800 antialiased"> <!-- Warna latar abu lembut dan teks gelap -->
    <div class="min-h-screen flex"> <!-- Wrapper flex penuh tinggi layar -->
        <!-- Sidebar Kiri (Navigasi Portal Admin) -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0">
            <div>
                <div class="p-6 pb-4">
                    <div class="text-xl font-extrabold tracking-tight text-slate-900">REKAYASA</div> <!-- Brand utama -->
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mt-0.5">Sport Center Malang</div> <!-- Sub-brand -->
                    <div class="mt-4 inline-block px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[11px] font-bold tracking-wider uppercase">
                        Portal Admin <!-- Penanda portal khusus admin -->
                    </div>
                </div>

                <!-- Menu Navigasi Admin -->
                <nav class="px-3 space-y-1 text-sm font-medium">
                    <!-- Link ke Dashboard Admin; aktif jika route saat ini admin.dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>Dashboard</span>
                    </a>
                    <!-- Link ke manajemen Kategori & Lapangan -->
                    <a href="{{ route('admin.courts') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.courts') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>Kategori & Lapangan</span>
                    </a>
                    <!-- Link ke daftar Booking & verifikasi pembayaran -->
                    <a href="{{ route('admin.bookings') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.bookings*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>Booking & Pembayaran</span>
                    </a>
                    <!-- Link ke Laporan Transaksi -->
                    <a href="{{ route('admin.reports') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>Laporan</span>
                    </a>
                </nav>
            </div>

            <!-- Bagian bawah sidebar: informasi jam operasional dan tombol Logout -->
            <div class="p-4 space-y-3 border-t border-slate-100">
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-xs text-slate-500">Jam operasional</div>
                    <div class="text-xs font-bold text-slate-800 mt-0.5">07:00–22:00 WIB</div> <!-- Jam operasional 07:00-22:00 -->
                </div>
                <form action="{{ route('logout') }}" method="POST"> <!-- Form POST untuk keluar sesi dengan aman -->
                    @csrf <!-- Token keamanan CSRF wajib di Laravel -->
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:text-red-600 rounded-xl hover:bg-red-50 transition">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar / Header atas -->
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between">
                <div class="text-sm text-slate-400">
                    Workspace / <span class="text-slate-600 font-medium">@yield('breadcrumb', 'Dashboard')</span> <!-- Breadcrumb dinamis -->
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-slate-400 hidden sm:inline">Akses terbatas admin</span> <!-- Keterangan hak akses -->
                    <span class="px-3.5 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold">
                        {{ auth()->user()->name ?? 'Administrator' }} <!-- Menampilkan nama admin yang sedang login -->
                    </span>
                </div>
            </header>

            <!-- Isi halaman tiap menu -->
            <main class="flex-1 p-8 max-w-7xl w-full mx-auto">
                <!-- Menampilkan pesan sukses dari session flash jika ada -->
                @if(session('success'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Menampilkan daftar pesan error validasi atau proses jika ada -->
                @if($errors->any())
                <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content') <!-- Tempat konten spesifik halaman disisipkan -->
            </main>

            <!-- Footer halaman admin -->
            <footer class="px-8 py-4 border-t border-slate-200/70 bg-white text-xs text-slate-400 flex flex-wrap justify-between gap-2">
                <span>© 2026 Rekayasa Sport Center Malang</span>
                <span>Portal Admin • Sistem Booking Lapangan</span>
            </footer>
        </div>
    </div>
    @yield('scripts') <!-- Slot script tambahan khusus per halaman -->
</body>

</html>