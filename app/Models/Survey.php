<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'mulai', 'batas', 'untuk', 'aktif'];
    protected $casts = ['mulai' => 'date', 'batas' => 'date', 'aktif' => 'boolean'];

    public function questions() { return $this->hasMany(Question::class)->orderBy('urutan'); }
    public function responses() { return $this->hasMany(Respon::class); }

    /** Pertanyaan yang tampil untuk role tertentu (semua + khusus role itu). */
    public function pertanyaanUntuk(string $role)
    {
        return $this->questions->filter(fn ($q) => in_array($q->untuk, ['semua', $role]))->values();
    }

    public function tertutup(): bool
    {
        return now()->lt($this->mulai->copy()->startOfDay()) || now()->gt($this->batas->copy()->endOfDay());
    }

    public function untukLabel(): string
    {
        return ['semua' => 'Mahasiswa dan dosen', 'mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen'][$this->untuk] ?? $this->untuk;
    }

    public function periode(): string
    {
        $a = $this->mulai->copy()->locale('id');
        $b = $this->batas->copy()->locale('id');
        return $a->format('mY') === $b->format('mY')
            ? $a->format('d') . '–' . $b->translatedFormat('d F Y')
            : $a->translatedFormat('d F') . ' – ' . $b->translatedFormat('d F Y');
    }
}
