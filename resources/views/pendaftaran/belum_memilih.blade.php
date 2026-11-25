@extends('layouts.app')

@section('title', 'Siswa Belum Memilih PKL')
@section('page-title', 'Daftar Siswa Yang Belum Memilih Tempat PKL')

@section('content')
<div class="space-y-6">

    <!-- Header & Export Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('pendaftaran.belum-memilih') }}" method="GET" class="w-full md:w-96 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa, NISN, kelas..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-100 focus:outline-none focus:border-blue-600">
        </form>

        <div class="flex items-center space-x-2">
            <a href="{{ route('pendaftaran.export-belum-memilih') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Belum Memilih (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Siswa</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">No. HP / WhatsApp</th>
                        <th class="px-6 py-4">Alamat</th>
                        <th class="px-6 py-4">Status PKL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($siswas as $i => $s)
                        <tr class="hover:bg-slate-800/40 transition-all">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $siswas->firstItem() + $i }}</td>
                            <td class="px-6 py-4 font-bold text-white">{{ $s->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $s->nisn_nip ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $s->kelas ?? 'XII TP 1' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-300">{{ $s->phone ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400 max-w-xs truncate">{{ $s->address ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($s->pendaftaran && $s->pendaftaran->status === 'ditolak')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold badge-tolak">DITOLAK (Belum Diterima)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold badge-belum">BELUM MEMILIH</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mb-2"></i>
                                <p>Luar biasa! Tidak ada siswa yang belum memilih tempat PKL.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $siswas->links() }}
        </div>
    </div>

</div>
@endsection
