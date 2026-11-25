@extends('layouts.app')

@section('title', 'Konfirmasi Pengajuan PKL')
@section('page-title', 'Konfirmasi & Verifikasi Tempat PKL Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('pendaftaran.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa atau bengkel..." 
                   class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
            <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:border-blue-600">
                <option value="">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                <option value="diterima" {{ $status === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>

        <div class="flex items-center space-x-2">
            <a href="{{ route('pendaftaran.export-diterima') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Diterima (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Table of Registrations -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Tempat PKL Pilihan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($pendaftarans as $i => $p)
                        <tr class="hover:bg-slate-800/40 transition-all">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $pendaftarans->firstItem() + $i }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $p->user->name }}</div>
                                <div class="text-xs text-slate-400">NISN: {{ $p->user->nisn_nip ?? '-' }} &bull; {{ $p->user->kelas }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-blue-400">{{ $p->bengkel->nama_bengkel ?? '-' }}</div>
                                <div class="text-xs text-slate-400 line-clamp-1">{{ $p->bengkel->alamat ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status === 'diterima')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold badge-terima">DITERIMA</span>
                                @elseif($p->status === 'ditolak')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold badge-tolak">DITOLAK</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold badge-pending">PENDING</span>
                                @endif
                                <div class="text-[11px] text-slate-500 mt-1">{{ $p->catatan }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-400">
                                {{ $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar)->format('d/m/Y H:i') : $p->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <form action="{{ route('pendaftaran.konfirmasi', $p->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="diterima">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg shadow" title="Terima Pendaftaran">
                                            Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('pendaftaran.konfirmasi', $p->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-lg shadow" title="Tolak Pendaftaran">
                                            Tolak
                                        </button>
                                    </form>
                                    <form action="{{ route('pendaftaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pengajuan ini agar siswa dapat memilih kembali?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg" title="Hapus Pengajuan">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                Tidak ada data pengajuan pendaftaran PKL yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $pendaftarans->links() }}
        </div>
    </div>

</div>
@endsection
