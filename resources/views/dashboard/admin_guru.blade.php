@extends('layouts.app')

@section('title', 'Dashboard Admin & Guru')
@section('page-title', 'Dashboard Monitoring & Verifikasi PKL')

@section('content')
<div class="space-y-6">

    <!-- Top Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-white mt-3">{{ $totalSiswa }}</div>
            <p class="text-xs text-slate-400 mt-1">Siswa Teknik Pemesinan</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sudah Diterima</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-400 mt-3">{{ $siswaDiterimaCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Telah dikonfirmasi industri</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu (Pending)</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-400 mt-3">{{ $siswaPendingCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Perlu verifikasi guru</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Belum Memilih</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-rose-400 mt-3">{{ $siswaBelumMemilihCount }}</div>
            <p class="text-xs text-slate-400 mt-1">Belum menentukan pilihan</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Bengkel Mitra</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <i class="fa-solid fa-industry"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-indigo-400 mt-3">{{ $totalBengkel }}</div>
            <p class="text-xs text-slate-400 mt-1">Industri / Bengkel aktif</p>
        </div>
    </div>

    <!-- Quick Export Action Buttons Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm font-bold text-white flex items-center space-x-2">
            <i class="fa-solid fa-file-excel text-emerald-400 text-lg"></i>
            <span>Export Laporan Excel Cepat:</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pendaftaran.export-diterima') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-download"></i>
                <span>Export Siswa Diterima (Excel)</span>
            </a>
            <a href="{{ route('pendaftaran.export-belum-memilih') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-download"></i>
                <span>Export Belum Memilih/Diterima (Excel)</span>
            </a>
            <a href="{{ route('bengkel.export') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-bold text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <i class="fa-solid fa-download"></i>
                <span>Export Data Bengkel</span>
            </a>
        </div>
    </div>

    <!-- Two Column Sections: Pending Applications & Students Not Chosen -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Pending Applications Needing Verification -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-amber-400 flex items-center space-x-2">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Pengajuan PKL Menunggu Konfirmasi</span>
                    </h4>
                    <a href="{{ route('pendaftaran.index') }}" class="text-xs text-blue-400 hover:underline font-semibold">Lihat Semua &rarr;</a>
                </div>

                @if($pendingPendaftaran->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingPendaftaran as $p)
                            <div class="p-3.5 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between gap-3">
                                <div>
                                    <h5 class="text-xs font-bold text-white">{{ $p->user->name }} <span class="text-slate-400 font-normal">({{ $p->user->kelas }})</span></h5>
                                    <p class="text-[11px] text-blue-400 mt-0.5">Pilih: {{ $p->bengkel->nama_bengkel ?? '-' }}</p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    <form action="{{ route('pendaftaran.konfirmasi', $p->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="diterima">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold rounded-lg shadow">Terima</button>
                                    </form>
                                    <form action="{{ route('pendaftaran.konfirmasi', $p->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <input type="hidden" name="catatan" value="Ditolak oleh pembimbing">
                                        <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-[11px] font-bold rounded-lg shadow">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-slate-500 text-xs">
                        <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mb-2"></i>
                        <p>Tidak ada pengajuan PKL yang tertunda (pending).</p>
                    </div>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 text-center">
                <a href="{{ route('pendaftaran.index') }}" class="text-xs text-blue-400 font-bold hover:underline">Kelola Seluruh Pengajuan Pendaftaran &rarr;</a>
            </div>
        </div>

        <!-- Students Yet to Choose -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-rose-400 flex items-center space-x-2">
                        <i class="fa-solid fa-user-xmark"></i>
                        <span>Siswa Belum Memilih Tempat PKL</span>
                    </h4>
                    <a href="{{ route('pendaftaran.belum-memilih') }}" class="text-xs text-blue-400 hover:underline font-semibold">Lihat Semua &rarr;</a>
                </div>

                @if($siswaBelumMemilih->count() > 0)
                    <div class="space-y-3">
                        @foreach($siswaBelumMemilih as $s)
                            <div class="p-3.5 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between">
                                <div>
                                    <h5 class="text-xs font-bold text-white">{{ $s->name }}</h5>
                                    <p class="text-[11px] text-slate-400 mt-0.5">NISN: <span class="font-mono text-slate-300">{{ $s->nisn_nip ?? '-' }}</span> &bull; Kelas: {{ $s->kelas }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/20">Belum Memilih</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center text-slate-500 text-xs">
                        <i class="fa-solid fa-award text-2xl text-emerald-500 mb-2"></i>
                        <p>Luar biasa! Semua siswa telah menentukan pilihan tempat PKL.</p>
                    </div>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 text-center">
                <a href="{{ route('pendaftaran.belum-memilih') }}" class="text-xs text-blue-400 font-bold hover:underline">Lihat Daftar Lengkap Siswa Belum Memilih &rarr;</a>
            </div>
        </div>

    </div>

</div>
@endsection
