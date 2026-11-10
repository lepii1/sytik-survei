<?php

use App\Http\Controllers\AdminController as A;
use App\Http\Controllers\AuthController as L;
use App\Http\Controllers\RespondenController as R;
use App\Http\Middleware\Peran;
use Illuminate\Support\Facades\Route;

// Tiga halaman login terpisah
Route::get('/', [L::class, 'form'])->defaults('role', 'mahasiswa');
Route::get('/login', fn () => redirect('/')); // halaman login mahasiswa ada di '/'
Route::post('/login', [L::class, 'masuk'])->defaults('role', 'mahasiswa')->middleware('throttle:10,1');
Route::get('/dosen/login', [L::class, 'form'])->defaults('role', 'dosen');
Route::post('/dosen/login', [L::class, 'masuk'])->defaults('role', 'dosen')->middleware('throttle:10,1');
Route::get('/admin/login', [L::class, 'form'])->defaults('role', 'admin');
Route::post('/admin/login', [L::class, 'masuk'])->defaults('role', 'admin')->middleware('throttle:10,1');
Route::post('/keluar', [L::class, 'keluar']);

// Mahasiswa & dosen (isi survei sesuai role)
Route::middleware(Peran::class . ':mahasiswa,dosen')->group(function () {
    Route::get('/dashboard', [R::class, 'dashboard']);
    Route::get('/survei/{id}', [R::class, 'detail']);
    Route::get('/survei/{id}/isi', [R::class, 'isi']);
    Route::post('/survei/{id}/simpan', [R::class, 'simpan']);
    Route::get('/survei/{id}/konfirmasi', [R::class, 'konfirmasi']);
    Route::post('/survei/{id}/kirim', [R::class, 'kirim']);
});

// Admin
Route::middleware(Peran::class . ':admin')->prefix('admin')->group(function () {
    Route::get('/', [A::class, 'dashboard']);
    Route::get('/survei/buat', [A::class, 'buat']);
    Route::post('/survei', [A::class, 'simpan']);
    Route::post('/survei/{id}/aktif', [A::class, 'aktif']);
    Route::post('/survei/{id}/hapus', [A::class, 'hapus']);
    Route::get('/survei/{id}/jawaban', [A::class, 'jawaban']);
    Route::get('/akun', [A::class, 'akun']);
    Route::post('/akun', [A::class, 'simpanAkun']);
    Route::post('/akun/{id}/hapus', [A::class, 'hapusAkun']);
});
