<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bengkel extends Model
{
    protected $fillable = [
        'nama_bengkel',
        'bidang_keahlian',
        'alamat',
        'nama_pemilik',
        'hp_pemilik',
        'kuota',
        'deskripsi',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranPkl::class, 'bengkel_id');
    }

    public function countDiterima()
    {
        return $this->pendaftaran()->where('status', 'diterima')->count();
    }

    public function countPending()
    {
        return $this->pendaftaran()->where('status', 'pending')->count();
    }

    public function isFull()
    {
        return $this->countDiterima() >= $this->kuota;
    }

    public function sisaKuota()
    {
        return max(0, $this->kuota - $this->countDiterima());
    }
}
