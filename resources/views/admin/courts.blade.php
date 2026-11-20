@extends('layouts.admin')
@section('title', 'Kategori & Lapangan')
@section('breadcrumb', 'Kategori & Lapangan')

@section('content')
<!-- Header halaman & tombol buka modal tambah lapangan -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Kategori & lapangan</h1>
        <p class="text-sm text-slate-500 mt-0.5">Atur kategori olahraga, tarif per jam, dan ketersediaan lapangan.</p>
    </div>
    <button onclick="document.getElementById('modalAddCourt').classList.remove('hidden')"
        class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
        + Tambah lapangan
    </button>
</div>

<!-- Bagian Kategori Olahraga -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-slate-900">Kategori olahraga</h2>
        <button onclick="document.getElementById('modalAddCategory').classList.remove('hidden')"
            class="text-xs font-bold text-indigo-600 hover:underline">
            + Tambah kategori
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($categories as $cat)
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-bold text-slate-900">{{ $cat->name }}</div> <!-- Nama kategori -->
                    <div class="text-xs text-slate-400 mt-0.5">{{ $cat->courts_count }} lapangan</div> <!-- Jumlah lapangan dalam kategori -->
                </div>
                <!-- Tombol buka modal edit kategori -->
                <button type="button" onclick="openEditCategory({{ $cat->id }}, '{{ $cat->name }}', {{ $cat->is_active ? 'true' : 'false' }})"
                    class="text-xs font-semibold text-indigo-600 hover:underline">Edit</button>
            </div>
            <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100">
                <!-- Form toggle status Aktif / Nonaktif kategori -->
                <form action="{{ route('admin.category.toggle', $cat) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $cat->is_active ? '● Aktif' : '○ Nonaktif' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Bagian Filter & Tabel Daftar Lapangan -->
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <!-- Form pencarian & filter lapangan -->
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama lapangan..."
            class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm w-64">
        <select name="category_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            <option value="">Semua kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm">
            <option value="">Semua status</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold">Filter</button>
        <a href="{{ route('admin.courts') }}" class="text-xs font-semibold text-indigo-600 px-2">Reset</a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="py-2.5 pr-4">Kode</th>
                    <th class="py-2.5 pr-4">Nama lapangan</th>
                    <th class="py-2.5 pr-4">Kategori</th>
                    <th class="py-2.5 pr-4">Harga / jam</th>
                    <th class="py-2.5 pr-4">Status</th>
                    <th class="py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($courts as $court)
                <tr>
                    <td class="py-3 pr-4 font-mono text-xs text-slate-500">{{ $court->code }}</td> <!-- Kode lapangan -->
                    <td class="py-3 pr-4 font-semibold text-slate-900">{{ $court->name }}</td> <!-- Nama lapangan -->
                    <td class="py-3 pr-4 text-slate-600">{{ $court->category->name }}</td> <!-- Kategori olahraga -->
                    <td class="py-3 pr-4 font-medium">Rp{{ number_format($court->price_per_hour, 0, ',', '.') }}</td> <!-- Harga per jam -->
                    <td class="py-3 pr-4">
                        <!-- Tombol toggle status Aktif / Nonaktif lapangan -->
                        <form action="{{ route('admin.court.toggle', $court) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $court->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $court->is_active ? '● Aktif' : '○ Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <!-- Tombol buka modal edit data lapangan -->
                        <button type="button"
                            onclick="openEditCourt({{ $court->id }}, '{{ $court->code }}', '{{ $court->name }}', {{ $court->category_id }}, {{ $court->price_per_hour }}, {{ $court->is_active ? 'true' : 'false' }})"
                            class="text-xs font-semibold text-indigo-600 hover:underline">
                            Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data lapangan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="modalAddCategory" class="hidden fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Tambah Kategori Olahraga</h3>
            <button onclick="document.getElementById('modalAddCategory').classList.add('hidden')" class="text-slate-400">✕</button>
        </div>
        <form action="{{ route('admin.category.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
                <input type="text" name="name" required placeholder="Contoh: Futsal" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked class="rounded"> Aktif
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalAddCategory').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditCategory" class="hidden fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Kategori Olahraga</h3>
            <button onclick="document.getElementById('modalEditCategory').classList.add('hidden')" class="text-slate-400">✕</button>
        </div>
        <form id="formEditCategory" method="POST" class="space-y-4">
            @csrf @method('PUT') <!-- Method PUT untuk update data -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
                <input id="editCatName" type="text" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input id="editCatActive" type="checkbox" name="is_active" value="1" class="rounded"> Aktif
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditCategory').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Lapangan -->
<div id="modalAddCourt" class="hidden fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold">Tambah lapangan</h3>
                <p class="text-xs text-slate-500">Lengkapi informasi lapangan baru.</p>
            </div>
            <button onclick="document.getElementById('modalAddCourt').classList.add('hidden')" class="text-slate-400">✕</button>
        </div>
        <form action="{{ route('admin.court.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode lapangan</label>
                    <input type="text" name="code" required placeholder="PDL-03" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama lapangan</label>
                    <input type="text" name="name" required placeholder="Padel 3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga per jam (Rp)</label>
                <input type="number" name="price_per_hour" required min="1000" placeholder="150000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <div class="bg-slate-50 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Status lapangan</div>
                    <div class="text-xs text-slate-500">Lapangan aktif tampil di katalog pelanggan.</div>
                </div>
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4">
            </div>
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-700 text-xs">
                Jam booking mengikuti operasional: 07:00–22:00 WIB.
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalAddCourt').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">Simpan lapangan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Lapangan -->
<div id="modalEditCourt" class="hidden fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Lapangan</h3>
            <button onclick="document.getElementById('modalEditCourt').classList.add('hidden')" class="text-slate-400">✕</button>
        </div>
        <form id="formEditCourt" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode lapangan</label>
                    <input id="editCourtCode" type="text" name="code" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama lapangan</label>
                    <input id="editCourtName" type="text" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                <select id="editCourtCategory" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga per jam (Rp)</label>
                <input id="editCourtPrice" type="number" name="price_per_hour" required min="1000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input id="editCourtActive" type="checkbox" name="is_active" value="1" class="rounded"> Aktif
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditCourt').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mengisi data ke form modal Edit Kategori lalu menampilkannya
    function openEditCategory(id, name, isActive) {
        document.getElementById('formEditCategory').action = `/admin/kategori/${id}`;
        document.getElementById('editCatName').value = name;
        document.getElementById('editCatActive').checked = isActive;
        document.getElementById('modalEditCategory').classList.remove('hidden');
    }

    // Mengisi data ke form modal Edit Lapangan lalu menampilkannya
    function openEditCourt(id, code, name, categoryId, price, isActive) {
        document.getElementById('formEditCourt').action = `/admin/lapangan/${id}`;
        document.getElementById('editCourtCode').value = code;
        document.getElementById('editCourtName').value = name;
        document.getElementById('editCourtCategory').value = categoryId;
        document.getElementById('editCourtPrice').value = price;
        document.getElementById('editCourtActive').checked = isActive;
        document.getElementById('modalEditCourt').classList.remove('hidden');
    }
</script>
@endsection