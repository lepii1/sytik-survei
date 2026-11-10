@extends('layouts.app')
@section('title', 'Formulir Survei')
@section('script')
@verbatim
<script>
function App() {
  const { user, survey, sections, saved } = D;
  const [step, setStep] = useState(0);
  const [a, setA] = useState(saved || {});
  const [busy, setBusy] = useState(false);
  const sec = sections[step], last = step === sections.length - 1;
  const filled = q => String(a[q.id] ?? '').trim() !== '';
  const doneOf = s => s.pertanyaan.filter(q => q.required && filled(q)).length;
  const reqOf = s => s.pertanyaan.filter(q => q.required).length;
  const totalDone = sections.reduce((n, s) => n + doneOf(s), 0);
  const totalReq = sections.reduce((n, s) => n + reqOf(s), 0);
  const set = (id, v) => setA({ ...a, [id]: v });
  const save = () => api(`/survei/${survey.id}/simpan`, { jawaban: a });
  const keluar = async () => { setBusy(true); await save(); location.href = '/dashboard'; };
  const lanjut = async () => {
    if (doneOf(sec) < reqOf(sec)) return alert('Lengkapi semua pertanyaan wajib (*) pada bagian ini.');
    setBusy(true); await save();
    last ? (location.href = `/survei/${survey.id}/konfirmasi`) : (setStep(step + 1), setBusy(false), scrollTo(0, 0));
  };

  const Q = ({ q }) => h`<div class="q"><b>${q.no}. ${q.teks}${q.required ? ' *' : ''}</b>
    ${q.tipe === 'skala' ? h`<div class="likert">${LABEL.map((l, i) => h`
      <button key=${i} class=${a[q.id] == i + 1 ? 'on' : ''} onClick=${() => set(q.id, i + 1)}><b>${i + 1}</b>${l}</button>`)}</div>` : null}
    ${q.tipe === 'pilihan' ? h`<div>${q.opsi.map(o => h`
      <button key=${o} class=${'pill ' + (a[q.id] === o ? 'on' : '')} onClick=${() => set(q.id, o)}>${a[q.id] === o ? '◉' : '○'} ${o}</button>`)}</div>` : null}
    ${q.tipe === 'teks' ? h`<textarea value=${a[q.id] || ''} placeholder="Tulis saran Anda di sini…" onInput=${e => set(q.id, e.target.value)}></textarea>` : null}
  </div>`;

  return h`<${Page} user=${user} active="dash">
    <div><h1 style="font-size:28px">Formulir pengisian survei</h1><p class="mu">${survey.judul}</p></div>
    <div class="grid g-form">
      <aside class="stack">
        <div class="card"><h2>Bagian survei</h2>
          <ul class="steps">${sections.map((s, i) => h`<li key=${i} class=${i === step ? 'now' : doneOf(s) === reqOf(s) ? 'done' : ''}>
            ${doneOf(s) === reqOf(s) && i !== step ? '✓ ' : ''}${i + 1}. ${s.judul}<div class="mu" style="font-weight:400">${doneOf(s)} dari ${reqOf(s)} wajib terjawab</div></li>`)}</ul>
          <div class="progress"><i style=${{ width: (totalReq ? totalDone / totalReq * 100 : 100) + '%' }}></i></div>
          <p class="mu">${totalDone}/${totalReq} jawaban wajib terisi</p></div>
        <${Notice} title="Hampir selesai!">Periksa jawaban, lalu lanjutkan ke halaman konfirmasi. Belum ada jawaban yang dikirim.<//>
        <${Btn} variant="ghost" disabled=${busy} onClick=${keluar}>Simpan dan keluar<//>
      </aside>
      <div class="card"><h2>${sec.judul}</h2><p class="mu">Bagian ${step + 1} dari ${sections.length} · Tanda * wajib diisi</p>
        ${sec.pertanyaan.map(q => h`<${Q} key=${q.id} q=${q} />`)}
        ${last && totalDone === totalReq ? h`<${Notice} tone="ok" icon="✓" title="Semua jawaban wajib sudah lengkap.">Anda bisa lanjut ke halaman konfirmasi.<//>` : null}
        <div class="row between" style="margin-top:20px">
          <${Btn} variant="ghost" disabled=${step === 0 || busy} onClick=${() => setStep(step - 1)}>← Sebelumnya<//>
          <span class="mu">Bagian ${step + 1} / ${sections.length}</span>
          <${Btn} disabled=${busy} onClick=${lanjut}>${last ? 'Berikutnya: konfirmasi' : 'Berikutnya →'}<//>
        </div>
      </div>
    </div>
  <//>`;
}
mount(App);
</script>
@endverbatim
@endsection
