<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKegiatan extends Model
{
    protected $table = 'absensi_kegiatan';

    protected $fillable = [
        'narapidana_id',
        'pengawas_id',
        'tanggal_waktu',
        'jenis_kegiatan',
        'bukti_file',
        'status_validasi',
        'catatan_pengawas',
    ];

    public function narapidana(): BelongsTo
    {
        return $this->belongsTo(User::class, 'narapidana_id', 'id');
    }

    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengawas_id', 'id');
    }
}
