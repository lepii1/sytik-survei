@extends('layouts.app')
@section('title', 'Dasbor Admin')
@section('script')
@verbatim
<script>
function App() {
  const { user, surveys, stat } = D;
  const act = async (url, msg) => { if (msg && !confirm(msg)) return; await api(url); location.reload(); };
  const S = ({ label, v }) => h`<div class="card stat"><p class="mu">${label}</p><b>${v}</b></div>`;
  return h`<${Page} user=${user} active="dash">
    <div class="banner"><h1>Panel admin SyTIK</h1><p>Kelola survei, akun responden, dan lihat jawaban yang masuk.</p></div>
    <div class="grid g4"><${S} label="Survei" v=${stat.survei} /><${S} label="Akun mahasiswa" v=${stat.mahasiswa} /><${S} label="Akun dosen" v=${stat.dosen} /><${S} label="Jawaban terkirim" v=${stat.terkirim} /></div>
    <div class="row between"><h1 style="font-size:22px">Daftar survei</h1><${Btn} href="/admin/survei/buat">+ Buat survei<//></div>
    <div class="card scroll">${surveys.length ? h`<table><thead><tr><th>Survei</th><th>Untuk</th><th>Periode</th><th>Pertanyaan</th><th>Terkirim</th><th>Status</th><th></th></tr></thead><tbody>
      ${surveys.map(s => h`<tr key=${s.id}><td><b style="color:var(--ink)">${s.judul}</b></td><td>${s.untuk}</td><td>${s.periode}</td><td>${s.pertanyaan}</td><td>${s.terkirim}</td>
        <td><${Badge} tone=${s.aktif ? 'ok' : ''}>${s.aktif ? 'Aktif' : 'Ditutup'}<//></td>
        <td><div class="row" style="flex-wrap:nowrap"><${Btn} variant="ghost sm" href=${'/admin/survei/' + s.id + '/jawaban'}>Lihat jawaban<//>
          <${Btn} variant="ghost sm" onClick=${() => act('/admin/survei/' + s.id + '/aktif')}>${s.aktif ? 'Tutup' : 'Buka'}<//>
          <${Btn} variant="ghost sm danger" onClick=${() => act('/admin/survei/' + s.id + '/hapus', 'Hapus survei beserta semua jawabannya?')}>Hapus<//></div></td></tr>`)}
    </tbody></table>` : h`<p class="mu">Belum ada survei. Klik "Buat survei" untuk memulai.</p>`}</div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
