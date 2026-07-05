<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['nama', 'email', 'nomor_induk', 'password', 'role', 'google_id', 'pembimbing_id', 'kinerja_draft'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // RELASI 1: Jika User ini adalah Klien, siapa PK pembimbingnya?
    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    // RELASI 2: Jika User ini adalah PK, siapa saja Klien anak bimbingannya?
    public function klienBimbingan()
    {
        return $this->hasMany(User::class, 'pembimbing_id');
    }
}
