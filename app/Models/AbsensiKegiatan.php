<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKegiatan extends Model
{
    // Menentukan nama tabel secara eksplisit karena sudah dibuat di DB
    protected $table = 'absensi_kegiatan';

    protected $fillable = [
        'narapidana_id',
        'tanggal_waktu',
        'jenis_kegiatan',
        'bukti_file',
        'status_validasi',
        'catatan_pengawas',
    ];

    // Relasi balik ke model User (Narapidana)
    public function narapidana(): BelongsTo
    {
        return $this->belongsTo(User::class, 'narapidana_id', 'id');
    }
}
