<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->input('role');
        $search = $request->input('search');

        $query = User::with('pendaftaran.bengkel');

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nisn_nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('user.index', compact('users', 'roleFilter', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,siswa',
            'nisn_nip' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,guru,siswa',
            'nisn_nip' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri!']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('user.index')->with('success', "Akun {$name} berhasil dihapus.");
    }

    public function exportExcel(Request $request)
    {
        $roleFilter = $request->input('role');
        $query = User::with('pendaftaran.bengkel');

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        $users = $query->get();
        $filename = "Daftar_Pengguna_" . ($roleFilter ?: 'Semua') . "_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Nama Lengkap',
                'Username',
                'Role/Peran',
                'NISN / NIP',
                'Kelas / Jabatan',
                'Email',
                'No HP / WhatsApp',
                'Alamat',
                'Status PKL',
                'Tempat PKL'
            ]);

            $no = 1;
            foreach ($users as $u) {
                $statusPkl = '-';
                $tempatPkl = '-';

                if ($u->role === 'siswa') {
                    if ($u->pendaftaran) {
                        $statusPkl = ucfirst($u->pendaftaran->status);
                        $tempatPkl = $u->pendaftaran->bengkel->nama_bengkel ?? '-';
                    } else {
                        $statusPkl = 'Belum Memilih';
                    }
                }

                fputcsv($file, [
                    $no++,
                    $u->name,
                    $u->username,
                    strtoupper($u->role),
                    $u->nisn_nip ?? '-',
                    $u->kelas ?? '-',
                    $u->email,
                    $u->phone ?? '-',
                    $u->address ?? '-',
                    $statusPkl,
                    $tempatPkl
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = "Template_Import_User_Siswa_Guru.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Lengkap', 'Username', 'NISN / NIP', 'Role (siswa/guru)', 'Email', 'Password', 'Kelas', 'No HP', 'Alamat']);
            fputcsv($file, ['Aditya Pratama', 'aditya01', '0054321099', 'siswa', 'aditya@siswa.smk.sch.id', 'password123', 'XII TP 2', '081298765432', 'Jl. Majapahit No 10']);
            fputcsv($file, ['Ir. Suprapto M.T.', 'suprapto', '197901012005011002', 'guru', 'suprapto@smk.sch.id', 'password123', 'Guru Pengampu CNC', '081322221111', 'Jl. Pemuda No 88']);
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

        if (($handle = fopen($path, 'r')) !== false) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $firstRow = true;
            $count = 0;
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                if (count($data) === 1 && str_contains($data[0], ';')) {
                    $data = str_getcsv($data[0], ';');
                }

                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                if (count($data) >= 5 && !empty(trim($data[0]))) {
                    $username = trim($data[1]);
                    $email = trim($data[4]);

                    // Avoid duplicate
                    if (User::where('username', $username)->orWhere('email', $email)->exists()) {
                        continue;
                    }

                    User::create([
                        'name' => trim($data[0]),
                        'username' => $username,
                        'nisn_nip' => trim($data[2] ?? ''),
                        'role' => in_array(strtolower(trim($data[3])), ['admin', 'guru', 'siswa']) ? strtolower(trim($data[3])) : 'siswa',
                        'email' => $email,
                        'password' => Hash::make(trim($data[5] ?: 'password123')),
                        'kelas' => trim($data[6] ?? 'XII TP 1'),
                        'phone' => trim($data[7] ?? ''),
                        'address' => trim($data[8] ?? ''),
                    ]);
                    $count++;
                }
            }
            fclose($handle);
            return redirect()->route('user.index')->with('success', "Berhasil mengimpor {$count} akun pengguna!");
        }

        return back()->withErrors(['file_excel' => 'Gagal membaca file excel/csv.']);
    }
}
