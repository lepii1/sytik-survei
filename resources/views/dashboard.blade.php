@extends('layouts.app')
@section('title', 'Dashboard Responden')
@section('script')
@verbatim
<script>
function App() {
  const { user, surveys, berhasil, tanggal } = D;
  const selesai = surveys.filter(s => s.dikirim).length;
  const sisa = surveys.length - selesai;
  const next = surveys.find(s => !s.dikirim && !s.tutup);
  return h`<${Page} user=${user} active="dash">
    ${berhasil
      ? h`<${Notice} tone="ok" icon="✓" title=${'Jawaban berhasil dikirim. Terima kasih, ' + berhasil.nama + '!'}>Survei ${berhasil.judul} telah selesai pada ${berhasil.waktu}.<//>`
      : h`<div class="banner"><h1>Halo, ${user.nama} 👋</h1><p>Setiap pengalaman berharga. Mari bantu layanan pendidikan menjadi lebih baik.</p></div>`}
    <div class="grid g3">
      <div class="card"><h2>Informasi responden</h2>
        <div class="row between" style="margin:12px 0"><div><b>${user.nama}</b><div class="mu">${user.peran === 'dosen' ? 'NIP ' + user.kode + ' · Dosen' : 'NIM ' + user.kode + ' · Mahasiswa'}</div></div><${Badge} tone="ok">Aktif<//></div>
        <p class="mu">Teknik Informatika</p></div>
      <div class="card stat"><p class="mu">Survei ditugaskan</p><b>${surveys.length}</b><p class="mu">Sesuai profil responden</p></div>
      <div class="card stat"><p class="mu">Sudah selesai</p><b>${selesai}</b><p class="mu">${sisa} survei masih tersedia</p></div>
    </div>
    <div class="row between"><h1 style="font-size:22px">Survei saya</h1><span class="mu">Diperbarui ${tanggal}</span></div>
    <div class="grid g3s">
      ${surveys.map(s => h`<article class="card sv" key=${s.id}>
        <div><${Badge} tone=${s.dikirim ? 'ok' : ''}>${s.dikirim ? '✓ Selesai diisi' : s.tutup ? 'Ditutup' : 'Tersedia'}<//></div>
        <h3>${s.judul}</h3><p class="mu">${s.periode}</p><p>${s.info}</p>
        <p class="mu">${s.dikirim ? 'Dikirim ' + s.dikirim : s.batas}</p>
        ${s.dikirim ? h`<p style="color:var(--ok);font-weight:600">✓ Pengisian selesai</p>` : null}
        <div><${Btn} variant=${s.dikirim ? 'ghost' : ''} href=${'/survei/' + s.id}>Lihat detail survei →<//></div>
      </article>`)}
    </div>
    ${surveys.length ? null : h`<${Notice} title="Belum ada survei untuk Anda.">Survei akan muncul di sini saat admin membukanya.<//>`}
    ${berhasil && next
      ? h`<p class="mu">Langkah berikutnya: isi ${next.judul} sebelum ${next.batas_penuh.split(',')[0]}.</p>`
      : h`<p class="mu">Jawaban dapat ditinjau kembali sebelum dikirim. Pilih survei untuk mulai mengisi.</p>`}
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
