<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bengkel;
use App\Models\PendaftaranPkl;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'siswa') {
            $pendaftaran = PendaftaranPkl::with('bengkel')->where('user_id', $user->id)->first();
            $totalBengkel = Bengkel::count();
            $bengkels = Bengkel::with('pendaftaran')->take(4)->get();
            return view('dashboard.siswa', compact('user', 'pendaftaran', 'totalBengkel', 'bengkels'));
        }

        // Guru & Admin Dashboard
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalBengkel = Bengkel::count();
        
        $siswaDiterimaCount = PendaftaranPkl::where('status', 'diterima')->count();
        $siswaPendingCount = PendaftaranPkl::where('status', 'pending')->count();
        
        // Siswa yang belum memilih tempat PKL (belum ada di tabel pendaftaran_pkl atau statusnya bukan 'diterima'/'pending')
        $registeredUserIds = PendaftaranPkl::whereIn('status', ['diterima', 'pending'])->pluck('user_id');
        $siswaBelumMemilihCount = User::where('role', 'siswa')
            ->whereNotIn('id', $registeredUserIds)
            ->count();

        // 5 Pengajuan Pending Terbaru
        $pendingPendaftaran = PendaftaranPkl::with(['user', 'bengkel'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        // Daftar Siswa Belum Memilih (Preview 5 data)
        $siswaBelumMemilih = User::where('role', 'siswa')
            ->whereNotIn('id', $registeredUserIds)
            ->take(5)
            ->get();

        return view('dashboard.admin_guru', compact(
            'user',
            'totalSiswa',
            'totalGuru',
            'totalBengkel',
            'siswaDiterimaCount',
            'siswaPendingCount',
            'siswaBelumMemilihCount',
            'pendingPendaftaran',
            'siswaBelumMemilih'
        ));
    }
}
