<div align="center">

# 🟣 SyTIK — Sistem Survei Kepuasan Prodi Teknik Informatika

Portal survei kepuasan **Program Studi Teknik Informatika, Politeknik Negeri Lhokseumawe** dengan 3 role: **Admin, Dosen, Mahasiswa**.

![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-18-61DAFB?logo=react&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/DB-SQLite-003B57?logo=sqlite&logoColor=white)
![Status](https://img.shields.io/badge/Project-Tugas%20Kampus-5423a5)

</div>

> Desain dari Figma **SyTIK-FULL** → Laravel + React **tanpa JSX dan tanpa build step**.
> React dipakai langsung di Blade (`resources/views`) lewat CDN + [htm](https://github.com/developit/htm), jadi **tidak perlu `npm install`**.

## ✨ Fitur per role

| Role | Halaman login | Bisa apa |
|---|---|---|
| 🛠️ **Admin** | `/admin/login` | Buat survei & pertanyaan, tentukan target (mahasiswa/dosen/semua), buka/tutup/hapus survei, daftarkan akun mahasiswa/dosen, lihat semua jawaban yang masuk |
| 👨‍🏫 **Dosen** | `/dosen/login` (NIP + nama) | Mengisi survei yang ditujukan untuk dosen, termasuk pertanyaan **khusus dosen** |
| 🎓 **Mahasiswa** | `/` (NIM + nama) | Mengisi survei yang ditujukan untuk mahasiswa, termasuk pertanyaan **khusus mahasiswa** |

Alur responden: login → dashboard → detail survei → isi formulir → konfirmasi → kirim.
Setiap pertanyaan punya pengaturan *"ditampilkan untuk"* (semua / khusus mahasiswa / khusus dosen), jadi satu survei bisa berisi pertanyaan berbeda untuk tiap role.

## 🧰 Teknologi

Laravel 11+ (auth, session, Eloquent, migration) · React 18 UMD + htm di Blade · SQLite · CSS biasa di `public/css/sytik.css`

## 🚀 Cara menjalankan (untuk teman yang clone)

**Syarat:** PHP 8.2+ (ekstensi `sqlite3`/`pdo_sqlite` aktif), [Composer](https://getcomposer.org/), Git, dan internet (React dimuat dari CDN).

```bash
git clone https://github.com/USERNAME/sytik-survei.git
cd sytik-survei
composer install
cp .env.example .env            # Windows CMD: copy .env.example .env
php artisan key:generate
touch database/database.sqlite  # Windows CMD: type nul > database\database.sqlite
php artisan migrate --seed
php artisan serve
```

Buka **http://127.0.0.1:8000**. Di `.env`, pastikan `DB_CONNECTION=sqlite` dan tambahkan `APP_TIMEZONE=Asia/Jakarta`.

### 🔑 Cara login

| Role | Halaman | Isi | Contoh (dari seeder) |
|---|---|---|---|
| Mahasiswa | `/` | **NIM + nama lengkap** | `2321401001` + `Nurul Aisyah` |
| Dosen | `/dosen/login` | **NIP + nama lengkap** | `198501012010011001` + `Dr. Irwansyah, S.T., M.Eng.` |
| Admin | `/admin/login` | username + kata sandi | `admin` + `admin12345` |

Mahasiswa dan dosen **tidak memakai kata sandi**: namanya harus sama dengan yang didaftarkan admin (huruf besar/kecil dan spasi ganda diabaikan). Akun mereka dibuat lewat **Admin → Akun responden**.

> ⚠️ Login tanpa kata sandi cukup untuk project kampus, tapi siapa pun yang tahu NIM + nama seseorang bisa masuk sebagai dia. Ganti kata sandi admin kalau dipublikasikan.

## 🗂️ Struktur penting

```
app/Http/Controllers/AuthController.php        # 3 login + logout
app/Http/Controllers/RespondenController.php   # dashboard, isi, kirim (mahasiswa & dosen)
app/Http/Controllers/AdminController.php       # survei, akun, lihat jawaban
app/Http/Middleware/Peran.php                  # pembatas akses per role
app/Models/{User,Survey,Question,Respon}.php
database/migrations/                           # tabel users(role), surveys, questions, responses
database/seeders/DatabaseSeeder.php           # akun & survei contoh
routes/web.php
resources/views/layouts/app.blade.php          # shell + CDN React
resources/views/partials/ui.blade.php          # komponen bersama
resources/views/login.blade.php                # 3 halaman login
resources/views/{dashboard,detail,isi,konfirmasi}.blade.php   # responden
resources/views/admin/{dashboard,buat,jawaban,akun}.blade.php # admin
```

## 🛠️ Troubleshooting

| Masalah | Solusi |
|---|---|
| Halaman hanya "Memuat…" | Cek internet (CDN diblokir?) lalu lihat Console browser |
| `could not find driver` | Aktifkan `pdo_sqlite` & `sqlite3` di `php.ini` |
| `No application encryption key` | `php artisan key:generate` |
| Error 419 / Page Expired | Hapus cookie, lalu `php artisan config:clear` |
| Login selalu gagal | Pastikan sudah `php artisan migrate --seed`. Nama harus sama dengan data terdaftar, dan tiap role punya halaman login sendiri |
| Ingin mengulang data | `php artisan migrate:fresh --seed` (semua data terhapus) |

<details>
<summary><b>👩‍💻 Untuk pemilik repo: membuat repo dari file zip ini</b></summary>

```bash
composer create-project laravel/laravel sytik-survei
cd sytik-survei
```

1. Salin semua isi folder zip ke `sytik-survei`, timpa file yang sama (`routes/web.php`, `app/Models/User.php`, `database/seeders/DatabaseSeeder.php`).
2. Jalankan langkah `migrate --seed` dan `serve` seperti di atas, lalu uji ketiga role.
3. Upload ke GitHub:

```bash
git init
git add .
git commit -m "SyTIK: portal survei 3 role"
git branch -M main
git remote add origin https://github.com/USERNAME/sytik-survei.git
git push -u origin main
```

File `.env` dan `database.sqlite` tidak ikut ter-push (sudah di `.gitignore` bawaan Laravel). Ganti `USERNAME` di README.
</details>

## 📌 Catatan

- Survei belum bisa **diedit** setelah dibuat (hanya dibuka/ditutup/dihapus). Menu "Panduan pengisian" dan "Bantuan" belum punya halaman.
- Dibuat untuk tugas kampus, bukan untuk produksi.

---
<div align="center">Dibuat dengan 💜 untuk Prodi Teknik Informatika · Politeknik Negeri Lhokseumawe</div>
