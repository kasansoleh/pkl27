<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranPkl extends Model
{
    protected $table = 'pendaftaran_pkl';

    protected $fillable = [
        'user_id',
        'bengkel_id',
        'status',
        'catatan',
        'tanggal_daftar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class, 'bengkel_id');
    }
}
