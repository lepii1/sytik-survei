@verbatim
<script>
// Komponen bersama. htm = sintaks mirip JSX dalam template string, jadi tidak butuh file .jsx
// Penghubung htm -> React: ubah style="a:b" (string) jadi objek dan class jadi className.
// Tanpa ini React melempar error dan seluruh halaman jadi kosong.
const css = t => Object.fromEntries(t.split(';').filter(x => x.trim()).map(r => {
  const [k, ...v] = r.split(':');
  return [k.trim().replace(/-([a-z])/g, (_, c) => c.toUpperCase()), v.join(':').trim()];
}));
const h = htm.bind((type, props, ...kids) => {
  if (props && typeof type === 'string') {
    props = { ...props };
    if (typeof props.style === 'string') props.style = css(props.style);
    if ('class' in props) { props.className = props.class; delete props.class; }
  }
  return React.createElement(type, props, ...kids);
});
const { useState } = React;
const D = window.__DATA__;
const csrf = document.querySelector('meta[name=csrf-token]').content;

const api = async (url, body = {}) => {
  const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) });
  return { ok: r.ok, ...(await r.json().catch(() => ({}))) };
};
const initials = n => n.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
const LABEL = ['Sangat tidak puas', 'Tidak puas', 'Cukup puas', 'Puas', 'Sangat puas'];

const Badge = ({ tone = '', children }) => h`<span class=${'badge ' + tone}>${children}</span>`;
const Btn = ({ variant = '', href, onClick, disabled, children }) => href && !disabled
  ? h`<a class=${'btn ' + variant} href=${href}>${children}</a>`
  : h`<button class=${'btn ' + variant} onClick=${onClick} disabled=${disabled}>${children}</button>`;
const Notice = ({ tone = '', icon = 'i', title, children }) =>
  h`<div class=${'notice ' + tone}><span>${icon}</span><div><b>${title}</b>${children}</div></div>`;

const Nav = ({ user, active }) => h`
  <header class="nav"><div class="nav-in">
    <a class="logo" href=${user ? (user.peran === 'admin' ? '/admin' : '/dashboard') : '/'}>SyTIK<small>Politeknik Negeri Lhokseumawe</small></a>
    <nav class="menu">
      ${!user ? h`<a href="/">Mahasiswa</a><a href="/dosen/login">Dosen</a><a href="/admin/login">Admin</a>`
        : user.peran === 'admin' ? h`<a class=${active === 'dash' ? 'on' : ''} href="/admin">Dasbor</a><a class=${active === 'buat' ? 'on' : ''} href="/admin/survei/buat">Buat survei</a><a class=${active === 'akun' ? 'on' : ''} href="/admin/akun">Akun responden</a>`
        : h`<a class=${active === 'dash' ? 'on' : ''} href="/dashboard">Survei saya</a><a href="#">Panduan pengisian</a><a href="#">Bantuan</a>`}
      ${user
        ? h`<${Badge}>${user.peran === 'admin' ? 'Admin' : user.peran === 'dosen' ? 'Dosen' : 'Mahasiswa'} · ${user.nama}<//>
            <a href="#" onClick=${async e => { e.preventDefault(); const r = await api('/keluar'); location.href = r.redirect; }}>Keluar</a>`
        : h`<${Badge}>Portal responden<//>`}
    </nav>
  </div></header>`;

const Foot = () => h`<footer class="foot"><span>© 2026 SyTIK · Program Studi Teknik Informatika · Politeknik Negeri Lhokseumawe</span><a href="#">Pusat bantuan ↗</a></footer>`;

const Page = ({ user, active, children }) => h`<${Nav} user=${user} active=${active} /><main class="wrap stack">${children}</main><${Foot} />`;
class Boundary extends React.Component {
  constructor(p) { super(p); this.state = { e: null }; }
  static getDerivedStateFromError(e) { return { e }; }
  render() {
    return this.state.e
      ? React.createElement('pre', { style: { whiteSpace: 'pre-wrap', padding: 24, color: '#b33d57' } }, '⚠ ' + this.state.e.message)
      : this.props.children;
  }
}
const mount = C => ReactDOM.createRoot(document.getElementById('app')).render(h`<${Boundary}><${C} /><//>`);
</script>
@endverbatim
