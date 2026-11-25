<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bengkel;
use App\Models\PendaftaranPkl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'name' => 'Administrator PKL',
            'username' => 'admin',
            'email' => 'admin@smk-pemesinan.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Pendidikan No. 1, Bengkel Utama SMK',
            'nisn_nip' => '198501012010011001',
            'kelas' => 'Kaprog Teknik Pemesinan',
        ]);

        // 2. Create Guru Pembimbing
        $guru1 = User::create([
            'name' => 'Drs. H. Bambang Sugipto, M.T.',
            'username' => 'guru',
            'email' => 'bambang@smk-pemesinan.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'phone' => '081398765432',
            'address' => 'Jl. Pemuda No. 45, Semarang',
            'nisn_nip' => '197403152002121002',
            'kelas' => 'Guru Pengampu Bubut/CNC',
        ]);

        $guru2 = User::create([
            'name' => 'Ir. Hendra Wijaya, S.Pd.',
            'username' => 'guru2',
            'email' => 'hendra@smk-pemesinan.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'phone' => '081567890123',
            'address' => 'Jl. Gajah Mada No. 12, Semarang',
            'nisn_nip' => '198008202008011005',
            'kelas' => 'Guru Pengampu Mold & Die',
        ]);

        // 3. Create Bengkel PKL
        $bengkel1 = Bengkel::create([
            'nama_bengkel' => 'Bengkel Bubut & Milling Precision "Karya Teknik Utama"',
            'bidang_keahlian' => 'Machining Bubut Manual, Milling & Driling',
            'alamat' => 'Kawasan Industri Terboyo Blok C-12, Semarang',
            'nama_pemilik' => 'H. Sugeng Rahardjo',
            'hp_pemilik' => '081122334455',
            'kuota' => 2, // sengaja dikunci 2 agar bisa dicoba skenario penuh!
            'deskripsi' => 'Melayani pembuatan sparepart mesin industri, roda gigi, dan poros presisi.',
        ]);

        $bengkel2 = Bengkel::create([
            'nama_bengkel' => 'PT Jaya Machining & Mold Die',
            'bidang_keahlian' => 'CNC Milling 3-Axis, Mold & Stamping Die',
            'alamat' => 'Jl. Kaligawe Raya Km. 7 No. 88, Semarang',
            'nama_pemilik' => 'Ir. Rudi Hermawan',
            'hp_pemilik' => '081299887766',
            'kuota' => 3,
            'deskripsi' => 'Spesialis cetakan plastik precision & manufaktur perkakas presisi.',
        ]);

        $bengkel3 = Bengkel::create([
            'nama_bengkel' => 'Bengkel Fabrikasi & Pengelasan "Surya Metalwork"',
            'bidang_keahlian' => 'Pengelasan SMAW, MIG/TIG, Konstruksi Baja',
            'alamat' => 'Jl. Majapahit No. 210, Pedurungan, Semarang',
            'nama_pemilik' => 'Suryono',
            'hp_pemilik' => '085712345678',
            'kuota' => 4,
            'deskripsi' => 'Manufaktur rangka mesin, conveyor belt, dan tangki tekanan sedang.',
        ]);

        $bengkel4 = Bengkel::create([
            'nama_bengkel' => 'Cipta Presisi CNC & Automation',
            'bidang_keahlian' => 'CAD/CAM Mastercam, CNC Lathe & Wire-EDM',
            'alamat' => 'Kawasan Industri Candi Gatot Subroto Blok 5 No. 3',
            'nama_pemilik' => 'Eng. Antonius Setiawan',
            'hp_pemilik' => '081800112233',
            'kuota' => 3,
            'deskripsi' => 'Pemrograman CNC modern, pemotongan presisi tinggi Wire-EDM.',
        ]);

        // 4. Create Siswa
        $siswaList = [
            ['name' => 'Rizky Pratama', 'username' => 'siswa1', 'nisn' => '0054321001', 'phone' => '089611112222', 'address' => 'Jl. Pedurungan Asri No. 4'],
            ['name' => 'Budi Santoso', 'username' => 'siswa2', 'nisn' => '0054321002', 'phone' => '089622223333', 'address' => 'Jl. Tlogosari Raya No. 18'],
            ['name' => 'Dimas Setiawan', 'username' => 'siswa3', 'nisn' => '0054321003', 'phone' => '089633334444', 'address' => 'Jl. Wolter Monginsidi No. 5'],
            ['name' => 'Fajar Nugroho', 'username' => 'siswa4', 'nisn' => '0054321004', 'phone' => '089644445555', 'address' => 'Jl. Genuk Indah No. 12'],
            ['name' => 'Eko Prasetyo', 'username' => 'siswa5', 'nisn' => '0054321005', 'phone' => '089655556666', 'address' => 'Jl. Bangetayu Wetan No. 8'],
            ['name' => 'Ahmad Fauzi', 'username' => 'siswa6', 'nisn' => '0054321006', 'phone' => '089666667777', 'address' => 'Jl. Gayamsari No. 9'],
            ['name' => 'Gilang Ramadhan', 'username' => 'siswa7', 'nisn' => '0054321007', 'phone' => '089677778888', 'address' => 'Jl. Kedungmundu No. 15'],
        ];

        $createdStudents = [];
        foreach ($siswaList as $s) {
            $createdStudents[] = User::create([
                'name' => $s['name'],
                'username' => $s['username'],
                'email' => $s['username'] . '@siswa.smk.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'phone' => $s['phone'],
                'address' => $s['address'],
                'nisn_nip' => $s['nisn'],
                'kelas' => 'XII TP 1',
            ]);
        }

        // 5. Create Sample Pendaftaran PKL
        // Bengkel 1 (Kuota 2) -> Penuh oleh Siswa 2 dan Siswa 4 (Diterima)
        PendaftaranPkl::create([
            'user_id' => $createdStudents[1]->id, // Budi Santoso
            'bengkel_id' => $bengkel1->id,
            'status' => 'diterima',
            'catatan' => 'Berkas lengkap, disetujui Kaprog.',
            'tanggal_daftar' => now()->subDays(5),
        ]);

        PendaftaranPkl::create([
            'user_id' => $createdStudents[3]->id, // Fajar Nugroho
            'bengkel_id' => $bengkel1->id,
            'status' => 'diterima',
            'catatan' => 'Lolos wawancara pemilik bengkel.',
            'tanggal_daftar' => now()->subDays(4),
        ]);

        // Bengkel 2 -> Pending (Dimas Setiawan)
        PendaftaranPkl::create([
            'user_id' => $createdStudents[2]->id, // Dimas Setiawan
            'bengkel_id' => $bengkel2->id,
            'status' => 'pending',
            'catatan' => 'Menunggu verifikasi guru pembimbing.',
            'tanggal_daftar' => now()->subDays(1),
        ]);

        // Siswa 1 (Rizky Pratama), Siswa 5, Siswa 6, Siswa 7 BELUM MEMILIH TEMPAT PKL.
    }
}
