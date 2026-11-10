@extends('layouts.app')
@section('title', 'Buat Survei')
@section('script')
@verbatim
<script>
const kosong = (bagian = '') => ({ bagian, teks: '', tipe: 'skala', opsi: '', wajib: true, untuk: 'semua' });
function App() {
  const { user, hariIni } = D;
  const [f, setF] = useState({ judul: '', deskripsi: '', mulai: hariIni, batas: hariIni, untuk: 'semua' });
  const [qs, setQs] = useState([kosong('Bagian 1')]);
  const [err, setErr] = useState('');
  const [busy, setBusy] = useState(false);
  const up = (i, k, v) => setQs(qs.map((q, j) => j === i ? { ...q, [k]: v } : q));
  const simpan = async () => {
    setBusy(true); setErr('');
    const body = { ...f, pertanyaan: qs.map(q => ({ ...q, opsi: q.tipe === 'pilihan' ? q.opsi.split('\n') : [] })) };
    const r = await api('/admin/survei', body);
    r.ok ? (location.href = r.redirect) : (setErr(r.error || 'Gagal menyimpan.'), setBusy(false));
  };
  const Sel = ({ v, on, opts }) => h`<select value=${v} onChange=${e => on(e.target.value)}>${opts.map(o => h`<option key=${o[0]} value=${o[0]}>${o[1]}</option>`)}</select>`;
  const UNTUK = [['semua', 'Mahasiswa dan dosen'], ['mahasiswa', 'Mahasiswa saja'], ['dosen', 'Dosen saja']];
  return h`<${Page} user=${user} active="buat">
    <div><h1 style="font-size:28px">Buat survei baru</h1><p class="mu">Atur identitas survei, lalu tambahkan pertanyaan. Pertanyaan bisa dikhususkan untuk mahasiswa atau dosen.</p></div>
    <div class="card"><h2>Informasi survei</h2>
      <label>Judul *<input value=${f.judul} onInput=${e => setF({ ...f, judul: e.target.value })} placeholder="Contoh: Layanan Perpustakaan" /></label>
      <label>Deskripsi<textarea value=${f.deskripsi} onInput=${e => setF({ ...f, deskripsi: e.target.value })}></textarea></label>
      <div class="grid g3s"><label>Tanggal mulai *<input type="date" value=${f.mulai} onInput=${e => setF({ ...f, mulai: e.target.value })} /></label>
        <label>Batas akhir *<input type="date" value=${f.batas} onInput=${e => setF({ ...f, batas: e.target.value })} /></label>
        <label>Untuk responden<${Sel} v=${f.untuk} on=${v => setF({ ...f, untuk: v })} opts=${UNTUK} /></label></div></div>
    ${qs.map((q, i) => h`<div class="card" key=${i}>
      <div class="row between"><h2>Pertanyaan ${i + 1}</h2>${qs.length > 1 ? h`<${Btn} variant="ghost sm danger" onClick=${() => setQs(qs.filter((_, j) => j !== i))}>Hapus<//>` : null}</div>
      <label>Teks pertanyaan *<input value=${q.teks} onInput=${e => up(i, 'teks', e.target.value)} /></label>
      <div class="grid g4"><label>Nama bagian *<input value=${q.bagian} onInput=${e => up(i, 'bagian', e.target.value)} /></label>
        <label>Tipe<${Sel} v=${q.tipe} on=${v => up(i, 'tipe', v)} opts=${[['skala', 'Skala 1–5'], ['pilihan', 'Pilihan'], ['teks', 'Isian teks']]} /></label>
        <label>Ditampilkan untuk<${Sel} v=${q.untuk} on=${v => up(i, 'untuk', v)} opts=${[['semua', 'Semua'], ['mahasiswa', 'Khusus mahasiswa'], ['dosen', 'Khusus dosen']]} /></label>
        <label>Wajib diisi<${Sel} v=${q.wajib ? '1' : '0'} on=${v => up(i, 'wajib', v === '1')} opts=${[['1', 'Wajib'], ['0', 'Opsional']]} /></label></div>
      ${q.tipe === 'pilihan' ? h`<label>Opsi (satu per baris, minimal 2)<textarea value=${q.opsi} onInput=${e => up(i, 'opsi', e.target.value)}></textarea></label>` : null}
    </div>`)}
    <div class="row"><${Btn} variant="ghost" onClick=${() => setQs([...qs, kosong(qs[qs.length - 1].bagian)])}>+ Tambah pertanyaan<//></div>
    ${err ? h`<${Notice} tone="warn" icon="!" title="Belum bisa disimpan">${err}<//>` : null}
    <div class="row"><${Btn} disabled=${busy} onClick=${simpan}>Simpan survei<//><${Btn} variant="ghost" href="/admin">Batal<//></div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
