@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6">

    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-900/40 via-slate-900 to-slate-900 border border-blue-500/20 rounded-2xl p-6 shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(ellipse_at_top_right,rgba(37,99,235,0.15),transparent_70%)] pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 text-blue-300 text-xs font-bold rounded-full uppercase tracking-wider">Siswa Teknik Pemesinan</span>
                <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-2">Halo, {{ $user->name }}!</h3>
                <p class="text-sm text-slate-300 mt-1">NISN: <span class="font-mono text-blue-400 font-bold">{{ $user->nisn_nip ?? '-' }}</span> &bull; Kelas: <span class="font-semibold text-slate-200">{{ $user->kelas }}</span></p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('bengkel.index') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-industry"></i>
                    <span>Pilih Tempat PKL</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Pendaftaran PKL Siswa -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-file-contract text-blue-500"></i>
            <span>Status Pengajuan PKL Anda</span>
        </h4>

        @if($pendaftaran)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <span class="text-xs text-slate-400 block mb-1">Bengkel Pilihan</span>
                    <span class="text-base font-bold text-white">{{ $pendaftaran->bengkel->nama_bengkel ?? '-' }}</span>
                    <p class="text-xs text-slate-400 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i> {{ $pendaftaran->bengkel->alamat ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block mb-1">Status Verifikasi</span>
                    <div>
                        @if($pendaftaran->status === 'diterima')
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold badge-terima">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>DITERIMA</span>
                            </span>
                        @elseif($pendaftaran->status === 'ditolak')
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold badge-tolak">
                                <i class="fa-solid fa-xmark-circle"></i>
                                <span>DITOLAK</span>
                            </span>
                        @else
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold badge-pending">
                                <i class="fa-solid fa-clock"></i>
                                <span>MENUNGGU KONFIRMASI (PENDING)</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Catatan: {{ $pendaftaran->catatan ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block mb-1">Kontak Industri</span>
                    <span class="text-sm font-semibold text-slate-200 block"><i class="fa-solid fa-user-tie mr-1 text-slate-400"></i> {{ $pendaftaran->bengkel->nama_pemilik ?? '-' }}</span>
                    <span class="text-sm font-semibold text-slate-200 block mt-1"><i class="fa-solid fa-phone mr-1 text-slate-400"></i> {{ $pendaftaran->bengkel->hp_pemilik ?? '-' }}</span>
                </div>
            </div>

            @if($pendaftaran->status === 'ditolak')
                <div class="mt-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs flex items-center justify-between">
                    <span>Pengajuan Anda ditolak. Silakan pilih bengkel lain yang kuotanya masih tersedia.</span>
                    <a href="{{ route('bengkel.index') }}" class="font-bold underline">Pilih Bengkel Lain</a>
                </div>
            @endif

            @if($pendaftaran->status === 'diterima')
                <div class="mt-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs">
                    <i class="fa-solid fa-circle-check mr-1"></i> Selamat! Anda telah diterima di tempat PKL ini. Silakan persiapkan diri dan koordinasi dengan guru pembimbing.
                </div>
            @endif
        @else
            <div class="p-8 text-center bg-slate-950 rounded-xl border border-dashed border-slate-800">
                <i class="fa-solid fa-folder-open text-3xl text-slate-600 mb-3"></i>
                <p class="text-sm font-semibold text-slate-300">Anda belum memilih tempat PKL.</p>
                <p class="text-xs text-slate-500 mt-1 mb-4">Silakan lihat daftar bengkel mitra industri Teknik Pemesinan dan tentukan pilihan Anda.</p>
                <a href="{{ route('bengkel.index') }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-lg shadow transition-all">
                    <span>Lihat Daftar Bengkel</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Preview Bengkel Tersedia -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-2">
                <i class="fa-solid fa-industry text-blue-500"></i>
                <span>Mitra Industri Teknik Pemesinan Terbaru</span>
            </h4>
            <a href="{{ route('bengkel.index') }}" class="text-xs text-blue-400 hover:underline font-semibold">Lihat Semua ({{ $totalBengkel }}) &rarr;</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($bengkels as $b)
                @php
                    $terisi = $b->countDiterima();
                    $isFull = $b->isFull();
                @endphp
                <div class="bg-slate-950 border {{ $isFull ? 'border-rose-500/30' : 'border-slate-800' }} rounded-xl p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded {{ $isFull ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                {{ $isFull ? 'PENUH' : 'TERSEDIA' }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">Kuota: {{ $terisi }}/{{ $b->kuota }}</span>
                        </div>
                        <h5 class="text-sm font-bold text-white line-clamp-2">{{ $b->nama_bengkel }}</h5>
                        <p class="text-xs text-blue-400 mt-1 font-medium">{{ $b->bidang_keahlian }}</p>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-2"><i class="fa-solid fa-location-dot mr-1"></i> {{ $b->alamat }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-900 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Pemilik: <strong>{{ $b->nama_pemilik }}</strong></span>
                        <a href="{{ route('bengkel.index') }}" class="text-blue-400 font-bold hover:underline">Detail</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
