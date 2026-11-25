@extends('layouts.app')

@section('title', 'Daftar Bengkel PKL')
@section('page-title', 'Daftar Bengkel & Industri Pasangan')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('bengkel.index') }}" method="GET" class="w-full md:w-96 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama bengkel, alamat, atau bidang..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
        </form>

        <div class="flex flex-wrap items-center gap-2">
            @if(in_array(auth()->user()->role, ['admin', 'guru']))
                <button onclick="openModal('modal-add-bengkel')" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Bengkel</span>
                </button>
                <div class="flex items-center space-x-1">
                    <button onclick="openModal('modal-import-bengkel')" class="p-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition-all" title="Import Excel/CSV">
                        <i class="fa-solid fa-file-import"></i>
                    </button>
                    <a href="{{ route('bengkel.export') }}" class="p-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition-all" title="Export ke Excel">
                        <i class="fa-solid fa-file-export"></i>
                    </a>
                </div>
            @else
                @if($pendaftaranSiswa && $pendaftaranSiswa->status === 'diterima')
                    <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold rounded-xl flex items-center space-x-2">
                        <i class="fa-solid fa-check-circle"></i>
                        <span>Anda sudah diterima di {{ $pendaftaranSiswa->bengkel->nama_bengkel }}</span>
                    </div>
                @elseif($pendaftaranSiswa && $pendaftaranSiswa->status === 'pending')
                    <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold rounded-xl flex items-center space-x-2">
                        <i class="fa-solid fa-clock"></i>
                        <span>Menunggu Konfirmasi di {{ $pendaftaranSiswa->bengkel->nama_bengkel }}</span>
                    </div>
                @else
                    <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold rounded-xl">
                        <i class="fa-solid fa-circle-info mr-1"></i> Pilih satu bengkel untuk mengajukan PKL
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Bengkel Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($bengkels as $b)
            @php
                $terisi = $b->countDiterima();
                $isFull = $b->isFull();
                $isChosen = $pendaftaranSiswa && $pendaftaranSiswa->bengkel_id === $b->id;
                $canChoose = auth()->user()->role === 'siswa' && (!$pendaftaranSiswa || $pendaftaranSiswa->status !== 'diterima') && !$isFull && !$isChosen;
            @endphp
            <div class="bg-slate-900 border {{ $isChosen ? 'border-blue-500/50 shadow-blue-900/10' : ($isFull ? 'border-rose-900/30' : 'border-slate-800') }} rounded-2xl shadow-lg flex flex-col h-full overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-xl">
                <!-- Status Header -->
                <div class="px-5 py-3 border-b border-slate-800 flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full {{ $isFull ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $isFull ? 'Kuota Penuh' : 'Kuota Tersedia' }}
                    </span>
                    <span class="text-[11px] font-mono font-bold text-slate-400">
                        {{ $terisi }} / {{ $b->kuota }} Siswa
                    </span>
                </div>

                <!-- Body -->
                <div class="p-5 flex-1">
                    <h3 class="text-base font-bold text-white leading-snug line-clamp-2 min-h-[3rem]">{{ $b->nama_bengkel }}</h3>
                    <p class="text-xs text-blue-400 font-bold mt-2 uppercase tracking-wide">{{ $b->bidang_keahlian }}</p>
                    
                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-start space-x-2 text-xs text-slate-400">
                            <i class="fa-solid fa-location-dot mt-0.5 text-slate-500"></i>
                            <span class="line-clamp-2 leading-relaxed">{{ $b->alamat }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-slate-300">
                            <i class="fa-solid fa-user-tie text-slate-500"></i>
                            <span class="font-semibold">{{ $b->nama_pemilik }} <span class="text-slate-500 font-normal">(Pemilik)</span></span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-slate-300">
                            <i class="fa-solid fa-phone text-slate-500"></i>
                            <span class="font-semibold font-mono tracking-wide">{{ $b->hp_pemilik }}</span>
                        </div>
                    </div>

                    @if($b->deskripsi)
                        <p class="mt-4 text-[11px] text-slate-500 italic leading-relaxed">{{ $b->deskripsi }}</p>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="px-5 py-4 bg-slate-950/50 border-t border-slate-800 flex items-center justify-between gap-2">
                    @if(in_array(auth()->user()->role, ['admin', 'guru']))
                        <button onclick="editBengkel({{ $b->toJson() }})" class="p-2 text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-all" title="Edit Bengkel">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="{{ route('bengkel.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus bengkel ini? Data pendaftaran siswa terkait juga akan terhapus.')" class="flex-none">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-600 rounded-lg transition-all" title="Hapus Bengkel">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->role === 'siswa')
                        @if($isChosen)
                            <div class="w-full text-center py-2 bg-blue-600/20 text-blue-400 border border-blue-600/30 text-[11px] font-bold rounded-lg flex items-center justify-center space-x-1">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>Pilihan Anda ({{ ucfirst($pendaftaranSiswa->status) }})</span>
                            </div>
                        @elseif($canChoose)
                            <form action="{{ route('bengkel.pilih') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="bengkel_id" value="{{ $b->id }}">
                                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition-all shadow shadow-blue-600/20 flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Ajukan Pendaftaran PKL</span>
                                </button>
                            </form>
                        @elseif($isFull)
                            <button disabled class="w-full py-2 bg-slate-800 text-slate-500 text-xs font-bold rounded-lg cursor-not-allowed border border-slate-700">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Kuota Sudah Penuh
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-slate-900 border border-dashed border-slate-800 rounded-2xl">
                <i class="fa-solid fa-industry text-4xl text-slate-700 mb-3"></i>
                <h4 class="text-slate-300 font-bold">Tidak ada data bengkel PKL</h4>
                <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian lain atau tambahkan data baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $bengkels->links() }}
    </div>

</div>

<!-- Modal Tambah Bengkel (Guru/Admin) -->
<div id="modal-add-bengkel" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/50">
            <h3 class="font-bold text-white text-lg flex items-center space-x-2">
                <i class="fa-solid fa-industry text-blue-500"></i>
                <span>Tambah Bengkel PKL Baru</span>
            </h3>
            <button onclick="closeModal('modal-add-bengkel')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('bengkel.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Bengkel / Industri</label>
                    <input type="text" name="nama_bengkel" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all" placeholder="Contoh: Bengkel Bubut CV. Jaya Terang">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all" placeholder="Pemesinan / CNC / Las">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kuota Siswa (Total)</label>
                    <input type="number" name="kuota" value="3" min="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" required rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all" placeholder="Alamat bengkel untuk penempatan..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Pemilik/Pembimbing</label>
                    <input type="text" name="nama_pemilik" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. HP/WA Pemilik</label>
                    <input type="text" name="hp_pemilik" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all" placeholder="0812...">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Singkat (Opsional)</label>
                    <input type="text" name="deskripsi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeModal('modal-add-bengkel')" class="px-5 py-2 text-sm font-bold text-slate-400 hover:text-white transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Bengkel (Guru/Admin) -->
<div id="modal-edit-bengkel" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-lg">Edit Data Bengkel PKL</h3>
            <button onclick="closeModal('modal-edit-bengkel')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="form-edit-bengkel" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Bengkel</label>
                    <input type="text" name="nama_bengkel" id="edit-nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" id="edit-bidang" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kuota Siswa</label>
                    <input type="number" name="kuota" id="edit-kuota" min="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" id="edit-alamat" required rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Pemilik</label>
                    <input type="text" name="nama_pemilik" id="edit-pemilik" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. HP/WA</label>
                    <input type="text" name="hp_pemilik" id="edit-hp" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all">
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeModal('modal-edit-bengkel')" class="px-5 py-2 text-sm font-bold text-slate-400 hover:text-white transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl transition-all">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Bengkel -->
<div id="modal-import-bengkel" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-lg flex items-center space-x-2">
                <i class="fa-solid fa-file-csv text-emerald-400"></i>
                <span>Import Data Bengkel PKL</span>
            </h3>
            <button onclick="closeModal('modal-import-bengkel')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6">
            <p class="text-xs text-slate-400 mb-6 leading-relaxed">Pilih file Excel/CSV yang berisi data bengkel. Gunakan template kami agar format data sesuai dengan sistem.</p>
            
            <a href="{{ route('bengkel.template') }}" class="mb-6 w-full flex items-center justify-center space-x-2 py-3 border border-slate-700 border-dashed rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-all text-xs font-bold">
                <i class="fa-solid fa-download"></i>
                <span>Download Template Import (CSV)</span>
            </a>

            <form action="{{ route('bengkel.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih File CSV/Excel</label>
                    <input type="file" name="file_excel" accept=".csv, .txt, .xls, .xlsx" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-100 focus:outline-none focus:border-blue-600">
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all text-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-file-import"></i>
                    <span>Proses Import Data</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    function editBengkel(bengkel) {
        document.getElementById('form-edit-bengkel').action = '/bengkel/' + bengkel.id;
        document.getElementById('edit-nama').value = bengkel.nama_bengkel;
        document.getElementById('edit-bidang').value = bengkel.bidang_keahlian;
        document.getElementById('edit-kuota').value = bengkel.kuota;
        document.getElementById('edit-alamat').value = bengkel.alamat;
        document.getElementById('edit-pemilik').value = bengkel.nama_pemilik;
        document.getElementById('edit-hp').value = bengkel.hp_pemilik;
        openModal('modal-edit-bengkel');
    }
</script>
@endsection
