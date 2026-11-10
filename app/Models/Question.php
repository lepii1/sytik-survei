<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;
    protected $fillable = ['survey_id', 'bagian', 'urutan', 'teks', 'tipe', 'opsi', 'wajib', 'untuk'];
    protected $casts = ['opsi' => 'array', 'wajib' => 'boolean'];
}
