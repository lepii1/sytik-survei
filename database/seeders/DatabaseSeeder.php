<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            // [nama, role, NIM/NIP/username, kata sandi (hanya admin)]
            ['Administrator SyTIK', 'admin', 'admin', 'admin12345'],
            ['Dr. Irwansyah, S.T., M.Eng.', 'dosen', '198501012010011001', null],
            ['Nurul Aisyah', 'mahasiswa', '2321401001', null],
        ];
        foreach ($akun as [$nama, $role, $id, $sandi]) {
            User::updateOrCreate(['identifier' => $id], ['name' => $nama, 'role' => $role,
                'email' => "$id@sytik.local", 'password' => Hash::make($sandi ?? Str::random(40))]);
        }

        if (Survey::count()) return;

        // [bagian, tipe, teks, wajib, untuk, opsi]
        $s1 = Survey::create(['judul' => 'Pembelajaran Semester Ganjil 2026/2027', 'untuk' => 'semua',
            'deskripsi' => 'Bagikan pengalaman Anda mengenai proses pembelajaran, akses bahan ajar, dan dukungan dosen.',
            'mulai' => now()->subDays(8), 'batas' => now()->addDays(21)]);
        $this->isi($s1, [
            ['Proses pembelajaran', 'skala', 'Dosen menyampaikan materi dengan jelas.', 1, 'mahasiswa'],
            ['Proses pembelajaran', 'skala', 'Jadwal perkuliahan berjalan sesuai rencana.', 1, 'semua'],
            ['Proses pembelajaran', 'skala', 'Bahan ajar mudah diakses dan dipahami.', 1, 'semua'],
            ['Dukungan pembelajaran', 'skala', 'Dosen mudah dihubungi untuk konsultasi.', 1, 'mahasiswa'],
            ['Dukungan pembelajaran', 'skala', 'Layanan akademik membantu kebutuhan saya.', 1, 'semua'],
            ['Dukungan mengajar (khusus dosen)', 'skala', 'Beban mengajar saya sesuai dengan bidang keahlian.', 1, 'dosen'],
            ['Dukungan mengajar (khusus dosen)', 'skala', 'Fasilitas ruang kelas mendukung kegiatan mengajar.', 1, 'dosen'],
            ['Penilaian keseluruhan', 'skala', 'Secara keseluruhan, saya puas dengan layanan pembelajaran.', 1, 'semua'],
            ['Penilaian keseluruhan', 'pilihan', 'Media pembelajaran yang paling sering digunakan.', 1, 'semua', ['LMS kampus', 'Konferensi video', 'Grup pesan']],
            ['Penilaian keseluruhan', 'teks', 'Saran untuk perbaikan pembelajaran (opsional)', 0, 'semua'],
        ]);

        $s2 = Survey::create(['judul' => 'Layanan Laboratorium', 'untuk' => 'mahasiswa',
            'deskripsi' => 'Nilai ketersediaan alat, kenyamanan ruang, dan dukungan asisten laboratorium.',
            'mulai' => now()->subDays(8), 'batas' => now()->addDays(16)]);
        $this->isi($s2, [
            ['Fasilitas laboratorium', 'skala', 'Peralatan laboratorium tersedia dan berfungsi baik.', 1, 'semua'],
            ['Fasilitas laboratorium', 'skala', 'Ruang laboratorium nyaman digunakan.', 1, 'semua'],
            ['Fasilitas laboratorium', 'skala', 'Asisten laboratorium membantu dengan baik.', 1, 'semua'],
            ['Fasilitas laboratorium', 'teks', 'Saran untuk laboratorium (opsional)', 0, 'semua'],
        ]);
    }

    private function isi(Survey $s, array $rows): void
    {
        foreach ($rows as $i => $r) {
            $s->questions()->create(['bagian' => $r[0], 'urutan' => $i + 1, 'tipe' => $r[1], 'teks' => $r[2],
                'wajib' => (bool) $r[3], 'untuk' => $r[4], 'opsi' => $r[5] ?? null]);
        }
    }
}
