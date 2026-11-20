<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Rekayasa Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="mb-6">
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Rekayasa Sport Center</div>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Daftar Akun Pelanggan</h1>
            <p class="text-xs text-slate-500 mt-1">Setelah mendaftar, Anda akan diarahkan ke halaman login terlebih dahulu.</p>
        </div>

        @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">Nomor HP</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-600">
            </div>

            <button type="submit"
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition">
                Daftar Akun
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:underline">Login di sini</a>
        </div>
    </div>
</body>

</html>