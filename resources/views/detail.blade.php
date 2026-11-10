@extends('layouts.app')
@section('title', 'Detail Survei')
@section('script')
@verbatim
<script>
function App() {
  const { user, survey: s, dikirim } = D;
  return h`<${Page} user=${user} active="dash">
    <p class="mu"><a href="/dashboard">Survei saya</a> / Detail survei</p>
    <div class="banner"><h1>${s.judul}</h1><p>Survei untuk ${s.untuk.toLowerCase()}</p></div>
    <div class="grid g2">
      <div class="stack">
        <div class="card"><h2>Tentang survei ini</h2><p style="margin:10px 0">${s.deskripsi || 'Berikan penilaian sesuai pengalaman Anda.'}</p>
          <div class="row"><${Badge}>${s.untuk}<//><${Badge}>${s.n} pertanyaan<//></div></div>
        <div class="card"><h2>Petunjuk pengisian</h2><ol style="line-height:1.9;padding-left:20px;margin-bottom:0">
          <li>Baca setiap pernyataan dengan cermat.</li>
          <li>Pilih penilaian 1–5, dari sangat tidak puas hingga sangat puas.</li>
          <li>Lengkapi pertanyaan wajib bertanda *. Saran tertulis bersifat opsional.</li>
          <li>Tinjau kembali jawaban Anda sebelum mengonfirmasi pengiriman.</li></ol></div>
      </div>
      <div class="card stack"><h2>Ringkasan survei</h2>
        <${Badge} tone=${dikirim ? 'ok' : ''}>${dikirim ? 'Sudah dikirim' : 'Terbuka untuk pengisian'}<//>
        <div><p class="mu">Periode pengisian</p><b style="color:var(--ink)">${s.periode}</b></div>
        <p class="mu">Batas akhir · ${s.batas_penuh}</p>
        <p>◷ Estimasi waktu: ${s.menit} menit</p><p>${s.bagian} bagian · ${s.wajib} wajib + ${s.n - s.wajib} opsional</p>
        <${Notice} title="Semua pertanyaan wajib perlu dijawab.">Anda akan meninjau jawaban sebelum mengirim.<//>
        <${Btn} href=${'/survei/' + s.id + '/isi'} disabled=${!!dikirim || s.tutup}>${dikirim ? 'Sudah dikirim' : s.tutup ? 'Periode berakhir' : 'Mulai isi survei →'}<//>
        <${Btn} variant="ghost" href="/dashboard">Kembali ke survei saya<//>
      </div>
    </div>
    <p class="mu">Responden: ${user.nama} · ${user.peran === 'dosen' ? 'NIP ' + user.kode + ' · Dosen' : 'NIM ' + user.kode + ' · Mahasiswa'}</p>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
