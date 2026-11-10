@extends('layouts.app')
@section('title', 'Masuk')
@section('script')
@verbatim
<script>
function App() {
  const L = D;
  const [f, setF] = useState({ id: '', nama: '', sandi: '' });
  const [err, setErr] = useState('');
  const [busy, setBusy] = useState(false);
  const go = async e => {
    e.preventDefault();
    setBusy(true); setErr('');
    const r = await api(L.action, f);
    r.ok ? (location.href = r.redirect) : (setErr(r.error || 'Terjadi kesalahan. Coba lagi.'), setBusy(false));
  };
  const lain = [['mahasiswa', '/', 'Mahasiswa'], ['dosen', '/dosen/login', 'Dosen'], ['admin', '/admin/login', 'Admin']].filter(x => x[0] !== L.role);
  return h`<${Page}>
    <div class="grid g-id">
      <div class="stack">
        <${Badge}>MULAI DARI SUARA ANDA<//>
        <h1 style="font-size:34px;line-height:1.2">Selamat datang di ruang partisipasi SyTIK.</h1>
        <p>Sistem Survei Kepuasan Program Studi Teknik Informatika, Politeknik Negeri Lhokseumawe.</p>
        <div class="hero">Kampus PNL</div>
        <${Notice} title="Butuh bantuan akun?">Akun didaftarkan oleh pengelola survei. Hubungi admin jika NIM/NIP atau nama Anda belum terdaftar.<//>
      </div>
      <form class="stack" onSubmit=${go}>
        <div><h1 style="font-size:26px">${L.judul}</h1><p class="mu">${L.sub}</p></div>
        <section class="card">
          <label>${L.label} *<input autoFocus value=${f.id} placeholder=${L.ph} onInput=${e => setF({ ...f, id: e.target.value })} /></label>
          ${L.sandi
            ? h`<label>Kata sandi *<input type="password" value=${f.sandi} onInput=${e => setF({ ...f, sandi: e.target.value })} /></label>`
            : h`<label>Nama lengkap *<input value=${f.nama} placeholder="Sesuai data yang didaftarkan" onInput=${e => setF({ ...f, nama: e.target.value })} /><small class="mu">Ketik sama seperti yang terdaftar (huruf besar/kecil tidak berpengaruh).</small></label>`}
          ${err ? h`<p class="err" style="margin-top:10px">${err}</p>` : null}
          <div style="margin-top:18px"><button class="btn" disabled=${busy || !f.id.trim() || !(L.sandi ? f.sandi : f.nama.trim())}>Masuk →</button></div>
        </section>
        <p class="mu">Bukan ${L.role === 'admin' ? 'admin' : L.role}? Masuk sebagai ${lain.map((x, i) => h`${i ? ' atau ' : ''}<a href=${x[1]}>${x[2]}</a>`)}.</p>
      </form>
    </div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
