<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    const HOME = ['admin' => '/admin', 'dosen' => '/dashboard', 'mahasiswa' => '/dashboard'];
    const LOGIN = ['admin' => '/admin/login', 'dosen' => '/dosen/login', 'mahasiswa' => '/'];
    const META = [
        'mahasiswa' => ['judul' => 'Masuk sebagai mahasiswa', 'label' => 'NIM', 'ph' => '2321401001', 'action' => '/login', 'sandi' => false,
            'sub' => 'Masuk dengan NIM dan nama lengkap sesuai data yang didaftarkan admin.'],
        'dosen' => ['judul' => 'Masuk sebagai dosen', 'label' => 'NIP', 'ph' => '198501012010011001', 'action' => '/dosen/login', 'sandi' => false,
            'sub' => 'Masuk dengan NIP dan nama lengkap sesuai data yang didaftarkan admin.'],
        'admin' => ['judul' => 'Masuk sebagai admin', 'label' => 'Nama pengguna', 'ph' => 'admin', 'action' => '/admin/login', 'sandi' => true,
            'sub' => 'Khusus pengelola survei Program Studi Teknik Informatika.'],
    ];

    public function form(Request $r)
    {
        $role = $r->route('role');
        if ($u = Auth::user()) return redirect(self::HOME[$u->role]);
        return view('login', ['data' => ['role' => $role] + self::META[$role]]);
    }

    /** Samakan huruf besar/kecil dan spasi ganda saat membandingkan nama. */
    private function norm(string $s): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($s)));
    }

    public function masuk(Request $r)
    {
        $role = $r->route('role');
        $id = trim((string) $r->input('id'));

        if ($role === 'admin') {
            $ok = Auth::attempt(['identifier' => $id, 'password' => (string) $r->input('sandi'), 'role' => 'admin']);
            if (!$ok) return response()->json(['error' => 'Nama pengguna atau kata sandi salah.'], 422);
        } else {
            // Mahasiswa & dosen: NIM/NIP + nama lengkap (tanpa kata sandi)
            $u = User::where('role', $role)->where('identifier', $id)->first();
            if (!$u || $this->norm($u->name) !== $this->norm((string) $r->input('nama'))) {
                return response()->json(['error' => ($role === 'dosen' ? 'NIP' : 'NIM') . ' atau nama tidak sesuai dengan data yang terdaftar.'], 422);
            }
            Auth::login($u);
        }

        $r->session()->regenerate();
        return response()->json(['redirect' => url(self::HOME[$role])]);
    }

    public function keluar(Request $r)
    {
        $role = Auth::user()?->role ?? 'mahasiswa';
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return response()->json(['redirect' => url(self::LOGIN[$role])]);
    }
}
