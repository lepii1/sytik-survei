<?php

namespace App\Http\Controllers;

use App\Models\Respon;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function profil(): array
    {
        $u = Auth::user();
        return ['nama' => $u->name, 'peran' => 'admin', 'kode' => $u->identifier];
    }

    private function fmt($d, $f = 'd M Y, H.i') { return $d->copy()->locale('id')->translatedFormat($f); }

    public function dashboard()
    {
        $surveys = Survey::withCount(['questions', 'responses as terkirim' => fn ($q) => $q->whereNotNull('dikirim_at')])
            ->latest()->get()->map(fn ($s) => [
                'id' => $s->id, 'judul' => $s->judul, 'periode' => $s->periode(), 'untuk' => $s->untukLabel(),
                'aktif' => $s->aktif, 'pertanyaan' => $s->questions_count, 'terkirim' => $s->terkirim,
            ]);
        return view('admin.dashboard', ['data' => ['user' => $this->profil(), 'surveys' => $surveys, 'stat' => [
            'survei' => $surveys->count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'terkirim' => Respon::whereNotNull('dikirim_at')->count(),
        ]]]);
    }

    public function buat()
    {
        return view('admin.buat', ['data' => ['user' => $this->profil(), 'hariIni' => now()->toDateString()]]);
    }

    public function simpan(Request $r)
    {
        $v = Validator::make($r->all(), [
            'judul' => 'required|string|max:150', 'deskripsi' => 'nullable|string|max:1000',
            'mulai' => 'required|date', 'batas' => 'required|date|after_or_equal:mulai',
            'untuk' => 'required|in:semua,mahasiswa,dosen',
            'pertanyaan' => 'required|array|min:1',
            'pertanyaan.*.bagian' => 'required|string|max:100', 'pertanyaan.*.teks' => 'required|string|max:500',
            'pertanyaan.*.tipe' => 'required|in:skala,pilihan,teks', 'pertanyaan.*.untuk' => 'required|in:semua,mahasiswa,dosen',
            'pertanyaan.*.wajib' => 'boolean', 'pertanyaan.*.opsi' => 'nullable|array',
        ], [], ['judul' => 'Judul', 'mulai' => 'Tanggal mulai', 'batas' => 'Batas akhir',
            'pertanyaan.*.teks' => 'Teks pertanyaan', 'pertanyaan.*.bagian' => 'Nama bagian']);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);

        foreach ($r->input('pertanyaan') as $i => $q) {
            $opsi = array_values(array_filter(array_map('trim', (array) ($q['opsi'] ?? []))));
            if ($q['tipe'] === 'pilihan' && count($opsi) < 2) {
                return response()->json(['error' => 'Pertanyaan ' . ($i + 1) . ' bertipe pilihan, isi minimal 2 opsi.'], 422);
            }
        }

        DB::transaction(function () use ($r) {
            $s = Survey::create($r->only('judul', 'deskripsi', 'mulai', 'batas', 'untuk') + ['aktif' => true]);
            foreach ($r->input('pertanyaan') as $i => $q) {
                $s->questions()->create([
                    'bagian' => $q['bagian'], 'urutan' => $i + 1, 'teks' => $q['teks'], 'tipe' => $q['tipe'],
                    'opsi' => $q['tipe'] === 'pilihan' ? array_values(array_filter(array_map('trim', $q['opsi']))) : null,
                    'wajib' => (bool) ($q['wajib'] ?? true), 'untuk' => $q['untuk'],
                ]);
            }
        });
        return response()->json(['redirect' => url('/admin')]);
    }

    public function aktif($id)
    {
        $s = Survey::findOrFail($id);
        $s->update(['aktif' => !$s->aktif]);
        return response()->json(['ok' => true]);
    }

    public function hapus($id)
    {
        Survey::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }

    public function jawaban($id)
    {
        $s = Survey::with('questions')->findOrFail($id);
        $resps = Respon::with('user')->where('survey_id', $id)->whereNotNull('dikirim_at')->latest('dikirim_at')->get();
        $belum = User::whereIn('role', $s->untuk === 'semua' ? ['mahasiswa', 'dosen'] : [$s->untuk])
            ->whereNotIn('id', $resps->pluck('user_id'))->orderBy('name')->get()
            ->map(fn ($u) => ['nama' => $u->name, 'peran' => $u->role, 'kode' => $u->identifier]);

        return view('admin.jawaban', ['data' => [
            'user' => $this->profil(),
            'survey' => ['id' => $s->id, 'judul' => $s->judul, 'periode' => $s->periode(), 'untuk' => $s->untukLabel()],
            'questions' => $s->questions->map(fn ($q) => ['id' => $q->id, 'bagian' => $q->bagian, 'teks' => $q->teks,
                'tipe' => $q->tipe, 'untuk' => $q->untuk])->values(),
            'responses' => $resps->map(fn ($x) => ['id' => $x->id, 'nama' => $x->user->name, 'peran' => $x->user->role,
                'kode' => $x->user->identifier, 'dikirim' => $this->fmt($x->dikirim_at), 'jawaban' => (object) ($x->jawaban ?? [])])->values(),
            'belum' => $belum,
        ]]);
    }

    public function akun()
    {
        $users = User::whereIn('role', ['mahasiswa', 'dosen'])
            ->withCount(['responses as terkirim' => fn ($q) => $q->whereNotNull('dikirim_at')])
            ->orderBy('role')->orderBy('name')->get()
            ->map(fn ($u) => ['id' => $u->id, 'nama' => $u->name, 'peran' => $u->role, 'kode' => $u->identifier, 'terkirim' => $u->terkirim]);
        return view('admin.akun', ['data' => ['user' => $this->profil(), 'akun' => $users]]);
    }

    public function simpanAkun(Request $r)
    {
        $v = Validator::make($r->all(), [
            'nama' => 'required|string|max:120', 'peran' => 'required|in:mahasiswa,dosen',
            'kode' => ['required', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,identifier'],
        ], ['kode.unique' => 'NIM / NIP tersebut sudah terdaftar.', 'kode.regex' => 'NIM / NIP hanya boleh huruf, angka, titik, minus, dan garis bawah.'],
            ['kode' => 'NIM / NIP', 'nama' => 'Nama']);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);

        // Tidak ada kata sandi: responden masuk dengan NIM/NIP + nama. Kolom password diisi acak.
        User::create(['name' => trim($r->nama), 'email' => strtolower($r->kode) . '@sytik.local',
            'password' => Hash::make(Str::random(40)), 'role' => $r->peran, 'identifier' => $r->kode]);
        return response()->json(['ok' => true]);
    }

    public function hapusAkun($id)
    {
        User::where('role', '!=', 'admin')->findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }
}
