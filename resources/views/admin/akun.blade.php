@extends('layouts.app')
@section('title', 'Akun Responden')
@section('script')
@verbatim
<script>
const PERAN = { mahasiswa: 'Mahasiswa', dosen: 'Dosen' };
function App() {
  const { user, akun } = D;
  const [f, setF] = useState({ nama: '', peran: 'mahasiswa', kode: '' });
  const [err, setErr] = useState('');
  const [tab, setTab] = useState('semua');
  const [busy, setBusy] = useState(false);
  const mhs = f.peran === 'mahasiswa';
  const buat = async () => {
    setBusy(true); setErr('');
    const r = await api('/admin/akun', f);
    r.ok ? location.reload() : (setErr(r.error || 'Gagal membuat akun.'), setBusy(false));
  };
  const hapus = async a => { if (confirm(`Hapus akun ${a.nama}? Jawaban miliknya ikut terhapus.`)) { await api(`/admin/akun/${a.id}/hapus`); location.reload(); } };
  return h`<${Page} user=${user} active="akun">
    <div><h1 style="font-size:28px">Akun responden</h1><p class="mu">Daftarkan mahasiswa (NIM) dan dosen (NIP). Mereka masuk dengan NIM/NIP + nama lengkap yang sama dengan yang didaftarkan di sini, tanpa kata sandi.</p></div>
    <div class="card"><h2>Tambah akun</h2>
      <div class="grid g3s"><label>Nama lengkap *<input value=${f.nama} onInput=${e => setF({ ...f, nama: e.target.value })} /></label>
        <label>Peran<select value=${f.peran} onChange=${e => setF({ ...f, peran: e.target.value })}><option value="mahasiswa">Mahasiswa</option><option value="dosen">Dosen</option></select></label>
        <label>${mhs ? 'NIM *' : 'NIP *'}<input value=${f.kode} placeholder=${mhs ? '2321401002' : '198501012010011002'} onInput=${e => setF({ ...f, kode: e.target.value })} /></label></div>
      ${err ? h`<p class="err" style="margin-top:10px">${err}</p>` : null}
      <div style="margin-top:16px"><${Btn} disabled=${busy} onClick=${buat}>Buat akun<//></div></div>
    <div class="tabs">${[['semua', 'Semua'], ['mahasiswa', 'Mahasiswa'], ['dosen', 'Dosen']].map(t => h`<button key=${t[0]} class=${tab === t[0] ? 'on' : ''} onClick=${() => setTab(t[0])}>${t[1]}</button>`)}</div>
    <div class="card scroll">${akun.filter(a => tab === 'semua' || a.peran === tab).length ? h`<table><thead><tr><th>Nama</th><th>Peran</th><th>NIM / NIP</th><th>Survei terkirim</th><th></th></tr></thead><tbody>
      ${akun.filter(a => tab === 'semua' || a.peran === tab).map(a => h`<tr key=${a.id}><td><b style="color:var(--ink)">${a.nama}</b></td><td>${PERAN[a.peran]}</td><td>${a.kode}</td><td>${a.terkirim}</td>
        <td><div class="row" style="flex-wrap:nowrap"><${Btn} variant="ghost sm danger" onClick=${() => hapus(a)}>Hapus<//></div></td></tr>`)}</tbody></table>` : h`<p class="mu">Belum ada akun.</p>`}</div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
