<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinerjaPk extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengawas_id', 'bulan', 'tahun',
        'litmas_kuota', 'litmas_berhasil', 'litmas_file', 'litmas_link',

        'pembimbingan_kuota', 'pembimbingan_berhasil', 'pembimbingan_detail', 'pembimbingan_file', 'pembimbingan_link',
        'pengawasan_kuota', 'pengawasan_berhasil', 'pengawasan_file', 'pengawasan_link',
        'rata_rata', 'predikat'
    ];

    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function getFilesAttribute()
    {
        // Sesuaikan list kategori
        $kategoris = ['litmas', 'pembimbingan', 'pengawasan'];
        $files = [];
        foreach ($kategoris as $k) {
            $files[$k] = json_decode($this->{$k . '_file'}, true) ?? [];
        }
        return $files;
    }
}
