@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Akun Siswa & Guru Pembimbing')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('user.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, NISN/NIP..." 
                   class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
            <select name="role" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                <option value="">Semua Peran (Role)</option>
                <option value="siswa" {{ $roleFilter === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="guru" {{ $roleFilter === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="openModal('modal-add-user')" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Pengguna</span>
            </button>
            <button onclick="openModal('modal-import-user')" class="p-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition-all" title="Import Excel">
                <i class="fa-solid fa-file-import"></i>
            </button>
            <a href="{{ route('user.export', ['role' => $roleFilter]) }}" class="p-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition-all" title="Export Excel">
                <i class="fa-solid fa-file-export"></i>
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Username / NISN/NIP</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4">Kelas / Jabatan</th>
                        <th class="px-6 py-4">Kontak / Alamat</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($users as $i => $u)
                        <tr class="hover:bg-slate-800/40 transition-all">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $users->firstItem() + $i }}</td>
                            <td class="px-6 py-4 font-bold text-white">
                                {{ $u->name }}
                                <div class="text-xs text-slate-400 font-normal">{{ $u->email }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-300">
                                {{ $u->username }}
                                <div class="text-[11px] text-slate-500">{{ $u->nisn_nip ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">ADMIN</span>
                                @elseif($u->role === 'guru')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">GURU</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">SISWA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $u->kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                <div><i class="fa-solid fa-phone mr-1"></i> {{ $u->phone ?? '-' }}</div>
                                <div class="line-clamp-1 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i> {{ $u->address ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="editUser({{ $u->toJson() }})" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg" title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('user.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-lg" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah User -->
<div id="modal-add-user" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-lg">Tambah Akun Pengguna Baru</h3>
            <button onclick="closeModal('modal-add-user')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('user.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Username</label>
                    <input type="text" name="username" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Peran (Role)</label>
                    <select name="role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru Pembimbing</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">NISN / NIP</label>
                    <input type="text" name="nisn_nip" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas / Jabatan</label>
                    <input type="text" name="kelas" value="XII TP 1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat</label>
                    <input type="text" name="address" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeModal('modal-add-user')" class="px-5 py-2 text-sm font-bold text-slate-400 hover:text-white">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modal-edit-user" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-lg">Edit Akun Pengguna</h3>
            <button onclick="closeModal('modal-edit-user')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="form-edit-user" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit-user-name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Username</label>
                    <input type="text" name="username" id="edit-user-username" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Peran</label>
                    <select name="role" id="edit-user-role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">NISN / NIP</label>
                    <input type="text" name="nisn_nip" id="edit-user-nisn" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</label>
                    <input type="text" name="kelas" id="edit-user-kelas" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" id="edit-user-email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">No. HP</label>
                    <input type="text" name="phone" id="edit-user-phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat</label>
                    <input type="text" name="address" id="edit-user-address" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100">
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeModal('modal-edit-user')" class="px-5 py-2 text-sm font-bold text-slate-400">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import User -->
<div id="modal-import-user" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-lg">Import Data Pengguna (Siswa/Guru)</h3>
            <button onclick="closeModal('modal-import-user')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6">
            <a href="{{ route('user.template') }}" class="mb-6 w-full flex items-center justify-center space-x-2 py-3 border border-slate-700 border-dashed rounded-xl text-slate-300 hover:bg-slate-800 transition-all text-xs font-bold">
                <i class="fa-solid fa-download"></i>
                <span>Download Template Import (CSV)</span>
            </a>
            <form action="{{ route('user.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih File CSV/Excel</label>
                    <input type="file" name="file_excel" accept=".csv, .txt" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-100">
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm shadow">Proses Import</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function editUser(u) {
        document.getElementById('form-edit-user').action = '/users/' + u.id;
        document.getElementById('edit-user-name').value = u.name;
        document.getElementById('edit-user-username').value = u.username;
        document.getElementById('edit-user-role').value = u.role;
        document.getElementById('edit-user-nisn').value = u.nisn_nip || '';
        document.getElementById('edit-user-kelas').value = u.kelas || '';
        document.getElementById('edit-user-email').value = u.email;
        document.getElementById('edit-user-phone').value = u.phone || '';
        document.getElementById('edit-user-address').value = u.address || '';
        openModal('modal-edit-user');
    }
</script>
@endsection
