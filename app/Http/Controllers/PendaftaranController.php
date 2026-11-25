<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranPkl;
use App\Models\Bengkel;
use App\Models\User;

class PendaftaranController extends Controller
{
    // List for Admin & Guru to manage applications
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = PendaftaranPkl::with(['user', 'bengkel']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn_nip', 'like', "%{$search}%");
            })->orWhereHas('bengkel', function($q) use ($search) {
                $q->where('nama_bengkel', 'like', "%{$search}%");
            });
        }

        $pendaftarans = $query->latest('tanggal_daftar')->paginate(15)->withQueryString();

        return view('pendaftaran.index', compact('pendaftarans', 'status', 'search'));
    }

    // Siswa chooses a Bengkel
    public function pilihBengkel(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'siswa') {
            return back()->withErrors(['error' => 'Hanya siswa yang dapat mendaftar tempat PKL.']);
        }

        $request->validate([
            'bengkel_id' => 'required|exists:bengkels,id',
        ]);

        $bengkel = Bengkel::findOrFail($request->bengkel_id);

        // 1. Cek kuota bengkel
        if ($bengkel->isFull()) {
            return back()->withErrors(['error' => 'Maaf, kuota bengkel ' . $bengkel->nama_bengkel . ' sudah penuh dipilih siswa lain. Silakan pilih bengkel lain.']);
        }

        // 2. Cek apakah siswa sudah punya pengajuan yang sudah diterima
        $existing = PendaftaranPkl::where('user_id', $user->id)->first();
        if ($existing && $existing->status === 'diterima') {
            return back()->withErrors(['error' => 'Anda sudah diterima di ' . $existing->bengkel->nama_bengkel . '. Pilihan tidak dapat diubah lagi.']);
        }

        if ($existing) {
            // Update existing choice
            $existing->update([
                'bengkel_id' => $bengkel->id,
                'status' => 'pending',
                'catatan' => 'Siswa mengajukan ulang pilihan tempat PKL.',
                'tanggal_daftar' => now(),
            ]);
        } else {
            // Create new
            PendaftaranPkl::create([
                'user_id' => $user->id,
                'bengkel_id' => $bengkel->id,
                'status' => 'pending',
                'catatan' => 'Menunggu verifikasi guru pembimbing / admin.',
                'tanggal_daftar' => now(),
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Berhasil mendaftar di ' . $bengkel->nama_bengkel . '! Silakan tunggu konfirmasi guru pembimbing.');
    }

    // Admin / Guru confirms selection (Terima / Tolak)
    public function konfirmasi(Request $request, PendaftaranPkl $pendaftaran)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        // If accepting, check quota
        if ($request->status === 'diterima') {
            $bengkel = $pendaftaran->bengkel;
            // Count others accepted in this bengkel
            $acceptedCount = PendaftaranPkl::where('bengkel_id', $bengkel->id)
                ->where('status', 'diterima')
                ->where('id', '!=', $pendaftaran->id)
                ->count();

            if ($acceptedCount >= $bengkel->kuota) {
                return back()->withErrors(['error' => "Gagal menerima siswa! Kuota bengkel {$bengkel->nama_bengkel} sudah penuh ({$bengkel->kuota} siswa)."]);
            }
        }

        $pendaftaran->update([
            'status' => $request->status,
            'catatan' => $request->catatan ?: ($request->status === 'diterima' ? 'Pengajuan disetujui oleh pembimbing.' : 'Pengajuan ditolak. Silakan ajukan ke tempat lain.'),
        ]);

        $statusText = $request->status === 'diterima' ? 'disetujui (diterima)' : 'ditolak';
        return back()->with('success', "Pengajuan PKL untuk {$pendaftaran->user->name} berhasil {$statusText}.");
    }

    // Admin / Guru deletes registration
    public function destroy(PendaftaranPkl $pendaftaran)
    {
        $namaSiswa = $pendaftaran->user->name;
        $pendaftaran->delete();

        return back()->with('success', "Pengajuan PKL siswa {$namaSiswa} berhasil dihapus. Siswa kini dapat memilih bengkel baru.");
    }

    // Admin / Guru melihat siapa saja yang belum memilih tempat PKL
    public function belumMemilih(Request $request)
    {
        $search = $request->input('search');

        // Siswa yang belum mendaftar sama sekali atau statusnya 'ditolak'
        $query = User::where('role', 'siswa')
            ->where(function($q) {
                $q->doesntHave('pendaftaran')
                  ->orWhereHas('pendaftaran', function($sub) {
                      $sub->where('status', 'ditolak');
                  });
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn_nip', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $siswas = $query->with('pendaftaran.bengkel')->paginate(15)->withQueryString();

        return view('pendaftaran.belum_memilih', compact('siswas', 'search'));
    }

    // Export Siswa yang Sudah Diterima ke Excel (CSV)
    public function exportDiterima()
    {
        $diterima = PendaftaranPkl::with(['user', 'bengkel'])
            ->where('status', 'diterima')
            ->get();

        $filename = "Siswa_PKL_Sudah_Diterima_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($diterima) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'NISN',
                'Nama Siswa',
                'Kelas / Jurusan',
                'No. HP Siswa',
                'Alamat Siswa',
                'Nama Bengkel PKL',
                'Bidang Bengkel',
                'Alamat Bengkel',
                'Nama Pemilik Bengkel',
                'No. HP Pemilik Bengkel',
                'Status Pendaftaran',
                'Tanggal Diterima',
                'Catatan Pembimbing'
            ]);

            $no = 1;
            foreach ($diterima as $p) {
                fputcsv($file, [
                    $no++,
                    $p->user->nisn_nip ?? '-',
                    $p->user->name,
                    $p->user->kelas ?? 'XII Teknik Pemesinan',
                    $p->user->phone ?? '-',
                    $p->user->address ?? '-',
                    $p->bengkel->nama_bengkel ?? '-',
                    $p->bengkel->bidang_keahlian ?? '-',
                    $p->bengkel->alamat ?? '-',
                    $p->bengkel->nama_pemilik ?? '-',
                    $p->bengkel->hp_pemilik ?? '-',
                    'DITERIMA',
                    $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-',
                    $p->catatan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export Siswa yang Belum Memilih & Belum Diterima ke Excel (CSV)
    public function exportBelumMemilih()
    {
        // Siswa yang belum memilih (tidak ada record) ATAU statusnya pending ATAU ditolak
        $siswas = User::where('role', 'siswa')
            ->where(function($q) {
                $q->doesntHave('pendaftaran')
                  ->orWhereHas('pendaftaran', function($sub) {
                      $sub->whereIn('status', ['pending', 'ditolak']);
                  });
            })
            ->with(['pendaftaran.bengkel'])
            ->get();

        $filename = "Siswa_PKL_Belum_Memilih_dan_Belum_Diterima_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($siswas) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'NISN',
                'Nama Siswa',
                'Kelas / Jurusan',
                'No. HP Siswa',
                'Alamat Siswa',
                'Status Pemilihan PKL',
                'Pilihan Bengkel (Bila Pending/Ditolak)',
                'Catatan Status'
            ]);

            $no = 1;
            foreach ($siswas as $s) {
                $statusText = 'Belum Memilih';
                $bengkelText = '-';
                $catatanText = 'Belum menentukan tempat PKL';

                if ($s->pendaftaran) {
                    if ($s->pendaftaran->status === 'pending') {
                        $statusText = 'Menunggu Konfirmasi (Pending)';
                        $bengkelText = $s->pendaftaran->bengkel->nama_bengkel ?? '-';
                        $catatanText = 'Sudah memilih, belum dikonfirmasi guru';
                    } elseif ($s->pendaftaran->status === 'ditolak') {
                        $statusText = 'Pengajuan Ditolak (Belum Diterima)';
                        $bengkelText = $s->pendaftaran->bengkel->nama_bengkel ?? '-';
                        $catatanText = $s->pendaftaran->catatan ?: 'Ditolak, perlu memilih bengkel lain';
                    }
                }

                fputcsv($file, [
                    $no++,
                    $s->nisn_nip ?? '-',
                    $s->name,
                    $s->kelas ?? 'XII Teknik Pemesinan',
                    $s->phone ?? '-',
                    $s->address ?? '-',
                    $statusText,
                    $bengkelText,
                    $catatanText
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
