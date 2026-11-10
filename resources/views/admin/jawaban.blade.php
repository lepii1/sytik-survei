@extends('layouts.app')
@section('title', 'Jawaban Survei')
@section('script')
@verbatim
<script>
const PERAN = { mahasiswa: 'Mahasiswa', dosen: 'Dosen' };
function App() {
  const { user, survey, questions, responses, belum } = D;
  const [pilih, setPilih] = useState(responses[0] ? responses[0].id : null);
  const [tab, setTab] = useState('semua');
  const daftar = responses.filter(r => tab === 'semua' || r.peran === tab);
  const cur = responses.find(r => r.id === pilih);
  const qs = cur ? questions.filter(q => q.untuk === 'semua' || q.untuk === cur.peran) : [];
  const tampil = q => { const v = cur.jawaban[q.id]; return v === undefined || v === '' ? '—' : q.tipe === 'skala' ? `${v} — ${LABEL[v - 1]}` : v; };
  return h`<${Page} user=${user} active="dash">
    <p class="mu"><a href="/admin">Dasbor</a> / Jawaban survei</p>
    <div class="banner"><h1>${survey.judul}</h1><p>${survey.periode} · untuk ${survey.untuk.toLowerCase()} · ${responses.length} jawaban terkirim</p></div>
    <div class="tabs">${[['semua', 'Semua'], ['mahasiswa', 'Mahasiswa'], ['dosen', 'Dosen']].map(t => h`<button key=${t[0]} class=${tab === t[0] ? 'on' : ''} onClick=${() => setTab(t[0])}>${t[1]}</button>`)}</div>
    <div class="grid g2" style="grid-template-columns:1fr 1.4fr">
      <div class="card scroll"><h2>Responden yang sudah mengirim</h2>
        ${daftar.length ? h`<table style="margin-top:10px"><thead><tr><th>Nama</th><th>Peran</th><th>Dikirim</th></tr></thead><tbody>
          ${daftar.map(r => h`<tr key=${r.id} class="klik" onClick=${() => setPilih(r.id)} style=${{ background: r.id === pilih ? 'var(--tint)' : '' }}><td><b style="color:var(--ink)">${r.nama}</b><div class="mu">${r.kode}</div></td><td>${PERAN[r.peran]}</td><td>${r.dikirim}</td></tr>`)}</tbody></table>` : h`<p class="mu" style="margin-top:10px">Belum ada jawaban.</p>`}
        ${belum.length ? h`<h2 style="margin-top:20px">Belum mengisi (${belum.length})</h2><p class="mu" style="margin-top:6px">${belum.map(b => b.nama + ' (' + PERAN[b.peran] + ')').join(', ')}</p>` : null}</div>
      <div class="card">${cur ? h`<div class="row between"><h2>${cur.nama}</h2><${Badge}>${PERAN[cur.peran]}<//></div><p class="mu">Dikirim ${cur.dikirim}</p>
        ${qs.map((q, i) => h`<div class="q" key=${q.id}><span class="mu">${q.bagian}</span><br/><b>${i + 1}. ${q.teks}</b><p style="margin-top:6px;color:var(--p);font-weight:600">${tampil(q)}</p></div>`)}`
        : h`<p class="mu">Pilih responden di sebelah kiri untuk melihat jawabannya.</p>`}</div>
    </div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
