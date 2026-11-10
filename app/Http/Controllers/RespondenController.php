<?php

namespace App\Http\Controllers;

use App\Models\Respon;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespondenController extends Controller
{
    private function profil(): array
    {
        $u = Auth::user();
        return ['nama' => $u->name, 'peran' => $u->role, 'kode' => $u->identifier];
    }

    private function cari($id): Survey
    {
        return Survey::with('questions')->where('aktif', true)
            ->whereIn('untuk', ['semua', Auth::user()->role])->findOrFail($id);
    }

    private function respon(Survey $s): Respon
    {
        return Respon::firstOrCreate(['survey_id' => $s->id, 'user_id' => Auth::id()]);
    }

    private function kartu(Survey $s, ?Respon $r): array
    {
        $q = $s->pertanyaanUntuk(Auth::user()->role);
        $n = $q->count();
        $menit = max(1, (int) ceil($n * 0.6));
        $id = fn ($d) => $d->copy()->locale('id');
        return [
            'id' => $s->id, 'judul' => $s->judul, 'deskripsi' => (string) $s->deskripsi, 'untuk' => $s->untukLabel(),
            'periode' => $s->periode(), 'info' => "$n pertanyaan · ±$menit menit", 'menit' => $menit,
            'batas' => 'Batas pengisian ' . $id($s->batas)->translatedFormat('d M Y'),
            'batas_penuh' => $id($s->batas)->translatedFormat('d F Y') . ', 23.59 WIB',
            'n' => $n, 'bagian' => $q->pluck('bagian')->unique()->count(), 'wajib' => $q->where('wajib', true)->count(),
            'tutup' => $s->tertutup(),
            'dikirim' => $r?->dikirim_at ? $id($r->dikirim_at)->translatedFormat('d M Y') : null,
        ];
    }

    private function sections(Survey $s): array
    {
        $no = 0;
        return $s->pertanyaanUntuk(Auth::user()->role)->groupBy('bagian')->map(function ($qs, $bagian) use (&$no) {
            return ['judul' => $bagian, 'pertanyaan' => $qs->map(function ($q) use (&$no) {
                return ['id' => $q->id, 'no' => ++$no, 'tipe' => $q->tipe, 'teks' => $q->teks,
                    'opsi' => $q->opsi ?? [], 'required' => $q->wajib];
            })->values()->all()];
        })->values()->all();
    }

    public function dashboard()
    {
        $role = Auth::user()->role;
        $resps = Respon::where('user_id', Auth::id())->get()->keyBy('survey_id');
        $surveys = Survey::with('questions')->where('aktif', true)->whereIn('untuk', ['semua', $role])
            ->orderBy('batas')->get()->map(fn ($s) => $this->kartu($s, $resps[$s->id] ?? null))->values();

        return view('dashboard', ['data' => [
            'user' => $this->profil(), 'surveys' => $surveys, 'berhasil' => session('berhasil'),
            'tanggal' => now()->locale('id')->translatedFormat('d F Y'),
        ]]);
    }

    public function detail($id)
    {
        $s = $this->cari($id);
        $k = $this->kartu($s, Respon::where(['survey_id' => $s->id, 'user_id' => Auth::id()])->first());
        return view('detail', ['data' => ['user' => $this->profil(), 'survey' => $k, 'dikirim' => $k['dikirim']]]);
    }

    public function isi($id)
    {
        $s = $this->cari($id);
        $r = $this->respon($s);
        if ($r->dikirim_at || $s->tertutup()) return redirect("/survei/$id");
        return view('isi', ['data' => ['user' => $this->profil(), 'survey' => $this->kartu($s, $r),
            'sections' => $this->sections($s), 'saved' => (object) ($r->jawaban ?? [])]]);
    }

    public function simpan(Request $request, $id)
    {
        $s = $this->cari($id);
        $r = $this->respon($s);
        if ($r->dikirim_at || $s->tertutup()) return response()->json(['error' => 'Survei sudah tidak dapat diubah.'], 409);

        $qs = $s->pertanyaanUntuk(Auth::user()->role)->keyBy('id');
        $bersih = [];
        foreach ((array) $request->input('jawaban', []) as $k => $v) {
            $q = $qs[(int) $k] ?? null;
            if (!$q || !is_scalar($v)) continue;
            $v = mb_substr(trim((string) $v), 0, 2000);
            if ($q->tipe === 'skala' && !in_array((int) $v, [1, 2, 3, 4, 5], true)) continue;
            if ($q->tipe === 'pilihan' && !in_array($v, $q->opsi ?? [], true)) continue;
            $bersih[(string) $k] = $v;
        }
        $r->update(['jawaban' => $bersih]);
        return response()->json(['ok' => true]);
    }

    public function konfirmasi($id)
    {
        $s = $this->cari($id);
        $r = $this->respon($s);
        if ($r->dikirim_at || $s->tertutup()) return redirect("/survei/$id");
        return view('konfirmasi', ['data' => ['user' => $this->profil(), 'survey' => $this->kartu($s, $r),
            'sections' => $this->sections($s), 'saved' => (object) ($r->jawaban ?? [])]]);
    }

    public function kirim($id)
    {
        $s = $this->cari($id);
        $r = $this->respon($s);
        if ($r->dikirim_at || $s->tertutup()) return response()->json(['error' => 'Survei sudah tidak dapat dikirim.'], 409);

        $jaw = $r->jawaban ?? [];
        foreach ($s->pertanyaanUntuk(Auth::user()->role) as $q) {
            if ($q->wajib && trim((string) ($jaw[$q->id] ?? '')) === '') {
                return response()->json(['error' => 'Masih ada pertanyaan wajib yang belum dijawab.'], 422);
            }
        }
        $r->update(['dikirim_at' => now()]);
        $t = now()->locale('id');
        session()->flash('berhasil', ['judul' => $s->judul, 'nama' => explode(' ', Auth::user()->name)[0],
            'waktu' => $t->translatedFormat('d F Y, H.i') . ' WIB']);
        return response()->json(['redirect' => url('/dashboard')]);
    }
}
