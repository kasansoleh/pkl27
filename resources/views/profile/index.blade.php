@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page-title', 'Pengaturan Profil & Keamanan Akun')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Update Profile Form -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
        <h3 class="text-sm font-bold uppercase tracking-wider text-blue-400 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-id-badge"></i>
            <span>Informasi Profil Pengguna</span>
        </h3>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Username (Login ID)</label>
                <input type="text" value="{{ $user->username }}" disabled
                       class="w-full bg-slate-950/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                <span class="text-[11px] text-slate-500 mt-1 block">Username tidak dapat diubah secara mandiri. Hubungi admin jika perlu perubahan.</span>
            </div>

            @if($user->role !== 'siswa')
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="0812...">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                <textarea name="address" rows="3"
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="Alamat domisili siswa...">{{ old('address', $user->address) }}</textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all text-sm flex items-center justify-center space-x-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Perubahan Profil</span>
            </button>
        </form>
    </div>

    <!-- Update Password Form -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
        <h3 class="text-sm font-bold uppercase tracking-wider text-amber-400 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Ubah Password Akun</span>
        </h3>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password Lama</label>
                <input type="password" name="current_password" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="••••••••">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password Baru</label>
                <input type="password" name="new_password" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="Minimal 6 karakter">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600" placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg shadow-amber-600/30 transition-all text-sm flex items-center justify-center space-x-2">
                <i class="fa-solid fa-key"></i>
                <span>Perbarui Password</span>
            </button>
        </form>
    </div>

</div>
@endsection
