<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Pelanggan') — Rekayasa Sport Center Malang</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar atas khusus Pelanggan (terpisah dari navigasi Admin) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('user.dashboard') }}" class="flex items-baseline gap-2">
                    <span class="text-lg font-extrabold tracking-tight text-slate-900">REKAYASA</span>
                    <span class="text-[11px] font-semibold text-emerald-600 uppercase">Sport Center</span>
                </a>
                <!-- Menu navigasi pelanggan -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('user.dashboard') }}"
                        class="{{ request()->routeIs('user.dashboard') || request()->routeIs('user.court.detail') ? 'text-emerald-600 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                        Katalog Lapangan <!-- Melihat kategori & lapangan -->
                    </a>
                    <a href="{{ route('user.bookings') }}"
                        class="{{ request()->routeIs('user.bookings*') ? 'text-emerald-600 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                        Booking & Pembayaran Saya <!-- Riwayat booking & pembayaran milik sendiri -->
                    </a>
                </nav>
            </div>

            <!-- Informasi pelanggan login, jam operasional, dan tombol Logout -->
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                    Buka 07:00–22:00 WIB
                </span>
                <span class="text-xs font-medium text-slate-700">{{ auth()->user()->name }}</span> <!-- Nama pelanggan aktif -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-red-50 hover:text-red-600 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten utama portal pelanggan -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        <!-- Notifikasi sukses -->
        @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- Notifikasi error validasi / aturan booking -->
        @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer portal pelanggan -->
    <footer class="bg-white border-t border-slate-200 py-4 px-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto flex justify-between">
            <span>© 2026 Rekayasa Sport Center Malang</span>
            <span>Jam Operasional: 07:00–22:00 WIB</span>
        </div>
    </footer>
    @yield('scripts')
</body>

</html>