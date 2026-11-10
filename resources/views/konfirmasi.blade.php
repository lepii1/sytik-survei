@extends('layouts.app')
@section('title', 'Konfirmasi Pengiriman')
@section('script')
@verbatim
<script>
function App() {
  const { user, survey, sections, saved: a } = D;
  const [ok, setOk] = useState(false);
  const [busy, setBusy] = useState(false);
  const filled = q => String(a[q.id] ?? '').trim() !== '';
  const rows = sections.map(s => { const r = s.pertanyaan.filter(q => q.required); const d = r.filter(filled).length; return { judul: s.judul, d, n: r.length }; });
  const lengkap = rows.every(r => r.d === r.n);
  const totalD = rows.reduce((n, r) => n + r.d, 0), totalN = rows.reduce((n, r) => n + r.n, 0);
  const ringkas = sections.flatMap(s => s.pertanyaan);
  const tampil = q => q.tipe === 'skala' ? `${a[q.id]} — ${LABEL[a[q.id] - 1]}` : a[q.id];
  const kirim = async () => {
    setBusy(true);
    const r = await api(`/survei/${survey.id}/kirim`);
    r.ok ? (location.href = r.redirect) : (alert(r.error || 'Gagal mengirim.'), setBusy(false));
  };
  return h`<${Page} user=${user} active="dash">
    <div><h1 style="font-size:28px">Tinjau sebelum mengirim</h1><p class="mu">${survey.judul}</p></div>
    <div class="grid g-conf">
      <div class="card"><h2>Ringkasan jawaban Anda</h2><p class="mu">Belum dikirim · Anda masih dapat mengubah jawaban</p>
        <table style="margin:14px 0"><thead><tr><th>Bagian survei</th><th>Kelengkapan</th><th>Status</th></tr></thead><tbody>
          ${rows.map(r => h`<tr key=${r.judul}><td>${r.judul}</td><td>${r.d}/${r.n} wajib</td><td>${r.d === r.n ? '✓ Lengkap' : '⚠ Belum lengkap'}</td></tr>`)}</tbody></table>
        <div class="row"><${Badge} tone=${lengkap ? 'ok' : ''}>${totalD}/${totalN} wajib terjawab<//><${Badge}>${ringkas.some(q => !q.required && filled(q)) ? 'Jawaban opsional terisi' : 'Jawaban opsional kosong'}<//></div>
        <div style="margin:14px 0">${ringkas.filter(filled).map(q => h`<p key=${q.id} style="margin-top:10px"><span class="mu">${q.no}. ${q.teks}</span><br/><b style="color:var(--ink)">${q.tipe === 'teks' ? '“' + a[q.id] + '”' : tampil(q)}</b></p>`)}</div>
        <${Btn} variant="ghost" href=${`/survei/${survey.id}/isi`}>← Kembali dan ubah jawaban<//>
      </div>
      <div class="card stack"><h2>Konfirmasi pengiriman</h2>
        <${Badge} tone=${lengkap ? 'ok' : ''}>${lengkap ? 'Siap dikirim' : 'Belum lengkap'}<//>
        <p>Pastikan seluruh jawaban telah mencerminkan pengalaman Anda.</p>
        <${Notice} tone="warn" title="Pengiriman mengonfirmasi jawaban.">Setelah dikirim, jawaban tidak dapat diubah melalui halaman ini.<//>
        <label style="display:flex;gap:8px;align-items:center;font-weight:500"><input type="checkbox" style="width:auto;margin:0" checked=${ok} onChange=${e => setOk(e.target.checked)} /> Saya sudah memeriksa seluruh jawaban.</label>
        <${Btn} disabled=${!ok || !lengkap || busy} onClick=${kirim}>Konfirmasi dan kirim<//>
        <${Btn} variant="ghost" href="/dashboard">Batal, kembali ke dashboard<//>
        <p class="mu">Jawaban baru tersimpan sebagai pengiriman setelah Anda menekan konfirmasi.</p>
      </div>
    </div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
