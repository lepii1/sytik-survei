<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $t) {
            $t->id();
            $t->string('judul');
            $t->text('deskripsi')->nullable();
            $t->date('mulai');
            $t->date('batas');
            $t->string('untuk')->default('semua'); // semua | mahasiswa | dosen
            $t->boolean('aktif')->default(true);
            $t->timestamps();
        });

        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $t->string('bagian');
            $t->unsignedInteger('urutan');
            $t->text('teks');
            $t->string('tipe'); // skala | pilihan | teks
            $t->json('opsi')->nullable();
            $t->boolean('wajib')->default(true);
            $t->string('untuk')->default('semua'); // pertanyaan khusus: semua | mahasiswa | dosen
        });

        Schema::create('responses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->json('jawaban')->nullable(); // {"id_pertanyaan": "nilai"}
            $t->timestamp('dikirim_at')->nullable();
            $t->timestamps();
            $t->unique(['survey_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('surveys');
    }
};
