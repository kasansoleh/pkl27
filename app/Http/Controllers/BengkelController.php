<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bengkel;
use App\Models\PendaftaranPkl;

class BengkelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Bengkel::with('pendaftaran');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_bengkel', 'like', "%{$search}%")
                  ->orWhere('bidang_keahlian', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%");
            });
        }

        $bengkels = $query->latest()->paginate(12)->withQueryString();

        $user = Auth::user();
        $pendaftaranSiswa = null;
        if ($user && $user->role === 'siswa') {
            $pendaftaranSiswa = PendaftaranPkl::where('user_id', $user->id)->first();
        }

        return view('bengkel.index', compact('bengkels', 'search', 'pendaftaranSiswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'bidang_keahlian' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nama_pemilik' => 'required|string|max:255',
            'hp_pemilik' => 'required|string|max:30',
            'kuota' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Bengkel::create($validated);

        return redirect()->route('bengkel.index')->with('success', 'Data bengkel PKL berhasil ditambahkan!');
    }

    public function update(Request $request, Bengkel $bengkel)
    {
        $validated = $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'bidang_keahlian' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nama_pemilik' => 'required|string|max:255',
            'hp_pemilik' => 'required|string|max:30',
            'kuota' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        $bengkel->update($validated);

        return redirect()->route('bengkel.index')->with('success', 'Data bengkel PKL berhasil diperbarui!');
    }

    public function destroy(Bengkel $bengkel)
    {
        $nama = $bengkel->nama_bengkel;
        $bengkel->delete();

        return redirect()->route('bengkel.index')->with('success', "Bengkel '{$nama}' berhasil dihapus.");
    }

    public function exportExcel()
    {
        $bengkels = Bengkel::with('pendaftaran')->get();
        $filename = "Daftar_Bengkel_PKL_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($bengkels) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens with proper Indonesian characters
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Nama Bengkel / Industri',
                'Bidang Keahlian',
                'Alamat Lengkap',
                'Nama Pemilik / Pembimbing Industri',
                'No. HP Pemilik',
                'Kuota Total',
                'Terisi (Diterima)',
                'Sisa Kuota',
                'Status Kuota',
                'Deskripsi Fasilitas'
            ]);

            $no = 1;
            foreach ($bengkels as $b) {
                $terisi = $b->countDiterima();
                $sisa = $b->sisaKuota();
                $status = ($sisa <= 0) ? 'PENUH' : 'TERSEDIA';

                fputcsv($file, [
                    $no++,
                    $b->nama_bengkel,
                    $b->bidang_keahlian,
                    $b->alamat,
                    $b->nama_pemilik,
                    $b->hp_pemilik,
                    $b->kuota,
                    $terisi,
                    $sisa,
                    $status,
                    $b->deskripsi ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = "Template_Import_Bengkel_PKL.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Bengkel', 'Bidang Keahlian', 'Alamat', 'Nama Pemilik', 'No HP Pemilik', 'Kuota', 'Deskripsi']);
            fputcsv($file, ['Bengkel Presisi Abadi', 'Bubut & Milling CNC', 'Kawasan Candi Blok A No 3', 'Bambang Irawan', '081234567890', '3', 'Fasilitas Mesin Bubut CNC & Milling']);
            fputcsv($file, ['PT Tri Graha Mold', 'Mold & Die Making', 'Jl. Raya Industri Km 12', 'Ir. Santoso', '085678901234', '2', 'Spesialis cetakan presisi injection']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file_excel');
        $path = $file->getRealPath();

        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Check BOM
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $firstRow = true;
            $count = 0;
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                // If line was separated by semicolon
                if (count($data) === 1 && str_contains($data[0], ';')) {
                    $data = str_getcsv($data[0], ';');
                }

                if ($firstRow) {
                    $firstRow = false;
                    continue; // Skip header
                }

                if (count($data) >= 6 && !empty(trim($data[0]))) {
                    Bengkel::create([
                        'nama_bengkel' => trim($data[0]),
                        'bidang_keahlian' => trim($data[1] ?? 'Teknik Pemesinan'),
                        'alamat' => trim($data[2]),
                        'nama_pemilik' => trim($data[3]),
                        'hp_pemilik' => trim($data[4]),
                        'kuota' => (int) trim($data[5] ?: 3),
                        'deskripsi' => trim($data[6] ?? ''),
                    ]);
                    $count++;
                }
            }
            fclose($handle);
            return redirect()->route('bengkel.index')->with('success', "Berhasil mengimpor {$count} data bengkel PKL!");
        }

        return back()->withErrors(['file_excel' => 'Gagal membaca file excel/csv.']);
    }
}
