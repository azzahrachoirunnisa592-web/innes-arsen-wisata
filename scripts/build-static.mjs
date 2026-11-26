import { cp, mkdir, rm, writeFile } from 'node:fs/promises';
import path from 'node:path';

const root = process.cwd();
const dist = path.join(root, 'dist');
const publicDir = path.join(root, 'public');

await rm(dist, { recursive: true, force: true });
await mkdir(dist, { recursive: true });

for (const folder of ['css', 'images', 'audio']) {
    await cp(path.join(publicDir, folder), path.join(dist, folder), { recursive: true });
}

await cp(path.join(publicDir, 'favicon.ico'), path.join(dist, 'favicon.ico'));
await cp(path.join(publicDir, 'robots.txt'), path.join(dist, 'robots.txt'));

const routes = [
    ['beranda', '/', 'Beranda'],
    ['destinasi', '/destinasi', 'Destinasi'],
    ['jelajah', '/jelajah', 'Jelajah'],
    ['budaya', '/budaya-sejarah', 'Budaya & Sejarah'],
    ['kuliner', '/kuliner', 'Kuliner'],
    ['profil', '/profil', 'Profil'],
    ['advertorial', '/advertorial', 'Advertorial'],
    ['berita', '/berita', 'Berita'],
    ['masuk', '/masuk', 'Masuk'],
    ['login-legacy', '/login', 'Login'],
    ['daftar', '/daftar', 'Daftar'],
    ['paket-legacy', '/paket', 'Paket Lama'],
    ['dashboard-pelanggan', '/dashboard', 'Pesanan Saya'],
    ['mou-pelanggan', '/mou', 'Kerja Sama MoU'],
    ['keluar', '/keluar', 'Keluar'],
    ['invoice-demo', '/invoice/1', 'Invoice Demo'],
    ['invoice-cetak-demo', '/invoice/1/cetak', 'Cetak Invoice Demo'],
    ['admin', '/admin', 'Panel Admin'],
    ['admin-paket', '/admin/paket', 'Kelola Paket'],
    ['admin-wisata', '/admin/wisata', 'Kelola Wisata'],
    ['admin-pesanan', '/admin/pesanan', 'Kelola Pesanan'],
    ['admin-pembayaran', '/admin/pembayaran', 'Kelola Pembayaran'],
    ['admin-konsumen', '/admin/konsumen', 'Kelola Konsumen'],
    ['admin-mou', '/admin/mou', 'Kelola MoU'],
    ['admin-pengaturan', '/admin/pengaturan', 'Pengaturan Admin'],
];

const packages = [
    {
        slug: 'dieng',
        name: 'Paket Wisata Dieng',
        place: 'Dieng, Jawa Tengah',
        category: 'Study Tour',
        duration: '3 hari · 2 malam',
        price: 'Rp 1.650.000',
        image: 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=85',
    },
    {
        slug: 'sidoarjo',
        name: 'Paket Wisata Sidoarjo',
        place: 'Sidoarjo, Jawa Timur',
        category: 'Kuliner',
        duration: '2 hari · 1 malam',
        price: 'Rp 1.343.001',
        image: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=900&q=85',
    },
    {
        slug: 'lombok',
        name: 'Jelajah Lombok',
        place: 'Lombok, Nusa Tenggara Barat',
        category: 'Pantai & Pulau',
        duration: '4 hari · 3 malam',
        price: 'Rp 2.750.000',
        image: 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=900&q=85',
    },
];

const articles = [
    {
        title: 'Menikmati fajar di dataran tinggi Dieng',
        category: 'Jelajah',
        image: 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=85',
        summary: 'Rencanakan perjalanan pagi dan nikmati udara sejuk serta lanskap pegunungan.',
    },
    {
        title: 'Mengenal nilai sejarah Borobudur',
        category: 'Budaya & Sejarah',
        image: 'https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&w=900&q=85',
        summary: 'Pelajari warisan budaya, arsitektur, dan aturan pelestarian saat berkunjung.',
    },
    {
        title: 'Mencicipi sajian khas Yogyakarta',
        category: 'Kuliner',
        image: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=900&q=85',
        summary: 'Temukan hidangan lokal dan cicipi cita rasa Nusantara selama perjalanan.',
    },
];

const publicRoutes = [
    ['beranda', '/', 'Beranda'],
    ['destinasi', '/destinasi', 'Destinasi'],
    ['jelajah', '/jelajah', 'Jelajah'],
    ['budaya', '/budaya-sejarah', 'Budaya & Sejarah'],
    ['kuliner', '/kuliner', 'Kuliner'],
    ['profil', '/profil', 'Profil'],
    ['advertorial', '/advertorial', 'Advertorial'],
    ['berita', '/berita', 'Berita'],
];

function nav(active) {
    return `
    <header class="site-header no-print">
      <nav class="navbar navbar-expand-xl nav-wrap container" aria-label="Navigasi situs">
        <a class="brand" href="/"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA &amp; TRAVEL</small></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
        <div class="navbar-collapse collapse" id="mainNavigation">
          <div class="nav-links ms-auto">
            ${publicRoutes.slice(1).map(([, href, label]) => `<a href="${href}" ${active === href ? 'class="active" aria-current="page"' : ''}>${label}</a>`).join('')}
            <a class="button button-small" href="/masuk">Masuk</a>
          </div>
        </div>
      </nav>
    </header>`;
}

function customerNav(active) {
    const links = [
        ['/dashboard', 'Pesanan'],
        ['/destinasi', 'Paket wisata'],
        ['/mou', 'MoU'],
        ['/masuk', 'Ganti akun'],
    ];
    return `<nav class="admin-nav" aria-label="Navigasi demo pelanggan">${links.map(([href, label]) => `<a href="${href}" ${active === href ? 'aria-current="page"' : ''}>${label}</a>`).join('')}</nav>`;
}

function adminNav(active) {
    const links = [
        ['/admin', 'Dashboard'],
        ['/admin/paket', 'Paket wisata'],
        ['/admin/wisata', 'Wisata'],
        ['/admin/pesanan', 'Pesanan'],
        ['/admin/pembayaran', 'Pembayaran'],
        ['/admin/konsumen', 'Konsumen'],
        ['/admin/mou', 'MoU'],
        ['/admin/pengaturan', 'Pengaturan'],
        ['/masuk', 'Ganti akun'],
    ];
    return `<nav class="admin-nav" aria-label="Navigasi demo admin">${links.map(([href, label]) => `<a href="${href}" ${active === href ? 'aria-current="page"' : ''}>${label}</a>`).join('')}</nav>`;
}

function footer() {
    return `
    <footer class="site-footer no-print">
      <div class="container footer-inner">
        <div class="footer-about">
          <a class="brand" href="/"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA &amp; TRAVEL</small></span></a>
          <p>Mitra perjalanan untuk menjelajahi destinasi pilihan di Indonesia.</p>
        </div>
        <div class="footer-links"><strong>Tautan cepat</strong><a href="/">Beranda</a><a href="/destinasi">Paket wisata</a><a href="/profil">Profil perusahaan</a><a href="/berita">Berita</a></div>
        <div class="footer-contact"><strong>Hubungi kami</strong><a href="/profil">Lihat profil perusahaan</a></div>
        <div class="footer-copyright">&copy; ${new Date().getFullYear()} PT. Innes Arsen Wisata.<br>Hak cipta dilindungi.</div>
      </div>
    </footer>`;
}

function page(title, active, body) {
    return `<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="PT. Innes Arsen Wisata - layanan perjalanan domestik, paket wisata, budaya, kuliner, dan study tour Indonesia.">
  <title>${title} - Innes Arsen Wisata</title>
  <link rel="icon" href="/favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/app.css">
  <link rel="stylesheet" href="/css/home.css">
  <link rel="stylesheet" href="/css/travel-cards.css">
  <link rel="stylesheet" href="/css/editorial.css">
  <link rel="stylesheet" href="/css/packages.css">
  <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
${nav(active)}
<main class="container page-content">${active.startsWith('/admin') ? adminNav(active) : ''}${['/dashboard', '/invoice/1', '/invoice/1/cetak', '/mou'].includes(active) ? customerNav(active) : ''}${body}</main>
${footer()}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.package-art-image').forEach((image) => {
  image.addEventListener('error', () => {
    const artwork = image.closest('.package-art');
    if (artwork) { image.hidden = true; artwork.classList.add('image-unavailable'); }
  }, { once: true });
});

const loginForm = document.querySelector('[data-demo-login]');
if (loginForm) {
  const message = loginForm.querySelector('[data-demo-login-message]');
  loginForm.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!loginForm.reportValidity()) return;
    const role = loginForm.querySelector('[name="demo-role"]')?.value || 'customer';
    sessionStorage.setItem('innes-demo-role', role);
    message.hidden = false;
    message.textContent = 'Demo login sebagai ' + (role === 'admin' ? 'admin' : 'pelanggan') + ' berhasil. Membuka dashboard simulasi...';
    window.setTimeout(() => { window.location.assign(role === 'admin' ? '/admin' : '/dashboard'); }, 700);
  });
}

const paymentStateKey = 'innes-demo-payment-paid';
function updatePaymentStatus(isPaid) {
  document.querySelectorAll('[data-demo-payment-status]').forEach((status) => {
    status.textContent = isPaid ? 'Lunas (simulasi)' : 'Menunggu pembayaran';
  });
  document.querySelectorAll('[data-demo-payment-simulate], [data-demo-payment-review]').forEach((button) => {
    button.disabled = isPaid;
    if (isPaid) button.textContent = 'Pembayaran sudah disimulasikan';
  });
}

if (localStorage.getItem(paymentStateKey) === 'true') updatePaymentStatus(true);

async function playPaymentSound() {
  try {
    const audio = new Audio('/audio/pembayaran-berhasil.mp3');
    await audio.play();
    return true;
  } catch (error) {
    console.warn('Audio not available or blocked by the browser.', error);
    return false;
  }
}

document.querySelectorAll('[data-demo-payment-simulate], [data-demo-payment-review]').forEach((button) => {
  button.addEventListener('click', async () => {
    localStorage.setItem(paymentStateKey, 'true');
    updatePaymentStatus(true);
    const message = document.querySelector('[data-demo-payment-message]');
    const soundPlayed = await playPaymentSound();
    if (message) {
      message.hidden = false;
      message.textContent = 'Pembayaran demo berhasil ditandai lunas.' + (soundPlayed ? '' : ' Suara tidak diputar karena diblokir browser atau tidak tersedia.');
    }
  });
});

document.querySelectorAll('[data-demo-action]').forEach((button) => {
  button.addEventListener('click', () => {
    const message = document.querySelector('[data-demo-action-message]');
    if (message) {
      message.hidden = false;
      message.textContent = 'Demo berhasil: aksi ini siap ditangani oleh backend Laravel pada deployment PHP/MySQL.';
    }

  });
});
</script>
</body>
</html>`;
}

function packageCards(limit = packages.length) {
    return packages.slice(0, limit).map((item) => `
      <article class="package-card">
        <div class="package-art has-image">
          <img class="package-art-image" src="${item.image}" alt="${item.place}" loading="lazy">
          <span>${item.category}</span>
        </div>
        <div class="package-body">
          <div class="muted small">${item.place}</div>
          <h3>${item.name}</h3>
          <div class="package-meta">${item.duration}</div>
          <div class="package-bottom"><strong>${item.price}<small> / orang</small></strong><a class="text-link" href="/paket/${item.slug}">Detail →</a></div>
        </div>
      </article>`).join('');
}

function articleCards() {
    return articles.map((article) => `
      <article class="article-card">
        <img src="${article.image}" alt="${article.title}" loading="lazy">
        <div class="article-copy">
          <span class="article-category">${article.category}</span>
          <h3><a href="/jelajah">${article.title}</a></h3>
          <p>${article.summary}</p>
        </div>
      </article>`).join('');
}

const home = `
<section class="home-hero">
  <div id="homeHero" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6500">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img class="home-hero-image" src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=2200&q=90" alt="Pemandangan pesisir dan pura di Bali">
        <div class="home-hero-shade"></div>
        <div class="home-hero-copy"><span class="hero-kicker">✦ Perjalanan Anda, cerita istimewa</span><h1 id="home-title">Jelajahi keindahan Indonesia bersama kami.</h1><p>Temukan destinasi pilihan, layanan perjalanan personal, dan pengalaman berkesan.</p><div class="hero-actions"><a class="button" href="/destinasi">Pesan paket sekarang →</a><a class="button button-outline-light" href="/jelajah">Jelajahi wisata</a></div></div>
      </div>
      <div class="carousel-item">
        <img class="home-hero-image" src="https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&w=2200&q=90" alt="Candi di Jawa Tengah">
        <div class="home-hero-shade"></div>
        <div class="home-hero-copy"><span class="hero-kicker">✦ Budaya dan sejarah</span><h1>Temukan cerita di setiap perjalanan.</h1><p>Dari warisan budaya hingga lanskap alam, rencanakan perjalanan sesuai minat Anda.</p><div class="hero-actions"><a class="button" href="/budaya-sejarah">Lihat budaya</a></div></div>
      </div>
    </div>
  </div>
</section>
<section class="section home-packages" id="destinasi">
  <div class="section-heading"><div><span class="eyebrow">Temukan perjalanan Anda</span><h2>Paket wisata unggulan</h2></div><a class="text-link" href="/destinasi">Lihat semua paket →</a></div>
  <div class="card-grid">${packageCards()}</div>
</section>
<section class="section inspiration-section" id="artikel">
  <div class="section-heading"><div><span class="eyebrow">Jelajahi lebih dalam</span><h2>Cerita dan inspirasi perjalanan</h2></div><span class="section-aside">Destinasi, budaya, dan kuliner Indonesia</span></div>
  <div class="article-grid">${articleCards()}</div>
</section>
<section class="company-panel" id="profil">
  <div class="company-copy"><span class="eyebrow">Mitra perjalanan Anda</span><h2>PT. Innes Arsen Wisata</h2><p>Mitra perjalanan untuk menjelajahi destinasi pilihan di Indonesia.</p><a class="text-link" href="/profil">Selengkapnya tentang perusahaan →</a></div>
  <div class="management"><h3>Tim manajemen</h3><div class="manager"><span class="manager-avatar">IA</span><div><strong>Tim Manajemen</strong><small>Innes Arsen Wisata</small></div></div></div>
</section>`;

const pageBodies = {
    '/': home,
    '/destinasi': `<section class="page-heading"><span class="eyebrow">Destinasi pilihan</span><h1>Paket wisata</h1><p>Temukan paket berdasarkan minat, budaya, kuliner, alam, dan study tour.</p></section><div class="card-grid">${packageCards()}</div>`,
    '/jelajah': `<section class="editorial-heading"><span class="eyebrow">Jelajah</span><h1>Wisata alam dan petualangan Indonesia</h1><p>Inspirasi perjalanan ke dataran tinggi, pesisir, dan sudut alam Nusantara.</p></section><div class="editorial-grid">${articleCards()}</div>`,
    '/budaya-sejarah': `<section class="editorial-heading"><span class="eyebrow">Budaya &amp; Sejarah</span><h1>Cerita budaya dan sejarah Nusantara</h1><p>Kenali warisan sejarah, tradisi, dan kehidupan lokal dari setiap perjalanan.</p></section><div class="editorial-grid">${articleCards().replaceAll('/jelajah', '/budaya-sejarah')}</div>`,
    '/kuliner': `<section class="editorial-heading"><span class="eyebrow">Kuliner</span><h1>Menjelajah rasa khas daerah</h1><p>Lengkapi perjalanan dengan mengenal hidangan lokal dan cita rasa Nusantara.</p></section><div class="editorial-grid">${articleCards().replaceAll('/jelajah', '/kuliner')}</div>`,
    '/profil': `<section class="page-heading"><span class="eyebrow">Profil perusahaan</span><h1>PT. Innes Arsen Wisata</h1></section><div class="profile-detail"><article class="profile-story"><h2>Mitra perjalanan Anda untuk menjelajahi destinasi pilihan di Indonesia.</h2><p>Kami melayani rencana perjalanan domestik, study tour, perjalanan grup, dan perjalanan berorientasi budaya serta kuliner.</p><a class="button" href="/destinasi">Lihat paket wisata</a></article><aside class="profile-contact"><h2>Informasi layanan</h2><div><strong>PT. Innes Arsen Wisata</strong><p>Paket wisata domestik dan perjalanan grup.</p></div></aside></div>`,
    '/advertorial': `<section class="editorial-heading"><span class="eyebrow">Advertorial</span><h1>Rekomendasi perjalanan bersama Innes Arsen Wisata</h1><p>Pilih paket yang sesuai waktu, jumlah peserta, destinasi, dan anggaran perjalanan.</p></section><div class="editorial-cta"><div><h2>Rencanakan perjalanan bersama tim kami</h2><p>Sesuaikan jadwal dan kebutuhan rombongan melalui halaman destinasi.</p></div><a class="button" href="/destinasi">Lihat Destinasi</a></div>`,
    '/berita': `<section class="editorial-heading"><span class="eyebrow">Berita</span><h1>Kabar dan panduan perjalanan</h1></section><div class="news-list"><article class="news-item"><span class="news-label">Panduan</span><h3>Periksa rincian paket sebelum memesan</h3><p>Pastikan jadwal, peserta, fasilitas, dan ketentuan sesuai kebutuhan.</p></article><article class="news-item"><span class="news-label">Informasi</span><h3>Persiapan sebelum berwisata</h3><p>Periksa cuaca, dokumen, kebutuhan kesehatan, dan perlengkapan perjalanan.</p></article><article class="news-item"><span class="news-label">Layanan</span><h3>Konfirmasi pemesanan</h3><p>Pastikan rincian pesanan telah dikonfirmasi sebelum pembayaran.</p></article></div>`,
    '/login': `<meta http-equiv="refresh" content="0;url=/masuk">`,
    '/paket': `<meta http-equiv="refresh" content="0;url=/destinasi">`,
    '/masuk': `<section class="auth-wrap">
        <div class="auth-intro">
            <span class="eyebrow">Selamat datang kembali</span>
            <h1>Masuk ke akun Anda.</h1>
            <p>Lihat status pesanan, lanjutkan pembayaran, atau pantau pengajuan kerja sama Anda.</p>
        </div>
        <form class="form-card form-stack" data-demo-login novalidate>
            <h2>Masuk</h2>
            <div class="notice" role="note">Versi statis Vercel ini hanya menampilkan demo. Autentikasi penuh, login akun, dan akses dashboard berjalan di backend Laravel.</div>
            <div class="alert alert-success" data-demo-login-message role="status" hidden></div>
            <label>Email<input type="email" name="email" autocomplete="email" placeholder="nama@email.com" required></label>
            <label>Kata sandi<input type="password" name="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required></label>
            <label>Masuk sebagai<select name="demo-role"><option value="customer">Pelanggan demo</option><option value="admin">Admin demo</option></select></label>
            <label class="check-row"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button class="button button-block" type="submit">Masuk ke akun</button>
            <p class="form-foot">Belum punya akun? <a href="/daftar">Daftar akun demo</a></p>
        </form>
    </section>`,
    '/daftar': `<section class="auth-wrap"><div class="auth-intro"><span class="eyebrow">Pendaftaran pelanggan</span><h1>Buat akun demo.</h1><p>Silakan gunakan halaman ini untuk memperagakan alur pendaftaran situs.</p></div><form class="form-card form-stack" data-demo-login><h2>Daftar</h2><div class="notice">Pendaftaran riil dan penyimpanan akun membutuhkan backend Laravel serta database MySQL.</div><div class="alert alert-success" data-demo-login-message role="status" hidden></div><label>Nama<input type="text" required></label><label>Email<input type="email" required></label><label>Kata sandi<input type="password" required></label><button class="button button-block" type="submit">Daftar akun demo</button><p class="form-foot">Sudah punya akun? <a href="/masuk">Masuk</a></p></form></section>`,
    '/dashboard': `<section class="page-heading"><span class="eyebrow">Demo dashboard pelanggan</span><h1>Pesanan saya</h1><p>Berikut tampilan simulasi setelah login berhasil.</p></section><div class="notice">Autentikasi dan data pesanan riil membutuhkan backend Laravel serta database MySQL.</div><div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Paket wisata</th><th>Keberangkatan</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr></thead><tbody><tr><td><a class="text-link" href="/invoice/1">INV-DEMO-001</a></td><td><a class="text-link" href="/paket/sidoarjo">Paket Wisata Sidoarjo</a></td><td>20 Des 2026</td><td>1 orang</td><td><a class="badge" href="/invoice/1" data-demo-payment-status>Menunggu pembayaran</a></td><td><a class="text-link" href="/invoice/1">Detail / bayar</a></td></tr></tbody></table></div><div class="hero-actions"><a class="button" href="/destinasi">Jelajahi paket wisata</a><a class="button button-light" href="/mou">Pengajuan MoU</a></div>`,
    '/mou': `<section class="page-heading"><span class="eyebrow">Demo kerja sama</span><h1>Pengajuan MoU</h1></section><div class="form-card form-stack"><div class="notice">Formulir ini hanya simulasi frontend. Pengiriman dokumen MoU riil dijalankan oleh backend Laravel.</div><label>Nama perusahaan<input type="text" placeholder="Nama perusahaan"></label><label>Pesan<textarea rows="4" placeholder="Tulis kebutuhan kerja sama"></textarea></label><button class="button" type="button" data-demo-action>Kirim simulasi MoU</button><div class="alert alert-success" data-demo-action-message role="status" hidden></div></div>`,
    '/keluar': `<section class="auth-wrap"><div class="auth-intro"><span class="eyebrow">Sesi demo</span><h1>Anda telah keluar.</h1><p>Versi statis ini tidak menyimpan sesi login atau data pengguna.</p></div><div class="form-card"><a class="button button-block" href="/masuk">Masuk kembali</a><a class="button button-light button-block" href="/">Kembali ke beranda</a></div></section>`,
    '/invoice/1': `<section class="page-heading"><span class="eyebrow">Demo invoice</span><h1>Invoice INV-DEMO-001</h1><p>Status: <a class="badge" href="/dashboard" data-demo-payment-status>Menunggu pembayaran</a></p></section><div class="payment-layout"><section class="panel"><h2>Ringkasan pesanan</h2><div class="summary-row"><span>Paket wisata</span><strong><a href="/paket/sidoarjo">Paket Wisata Sidoarjo</a></strong></div><div class="summary-row"><span>Jumlah peserta</span><strong>1 orang</strong></div><div class="summary-row summary-total"><span>Total tagihan</span><strong>Rp 1.343.001</strong></div><a class="button button-light" href="/invoice/1/cetak">Cetak invoice / kuitansi</a></section><section class="panel payment-panel"><span class="eyebrow">Instruksi pembayaran demo</span><h2>Pindai kode QR</h2><div class="notice">Demo frontend: QR ini hanya contoh profil pembayaran dan tidak memproses transfer atau mengonfirmasi pembayaran sungguhan.</div><img class="qris-image" src="/images/qris-dana.png" alt="Kode QR profil DANA untuk contoh instruksi pembayaran"><p class="small muted">Untuk pembayaran nyata, periksa penerima dan nominal secara manual sebelum transfer.</p><button class="button button-block" type="button" data-demo-payment-simulate>Simulasi pembayaran berhasil</button><div class="alert alert-success" data-demo-payment-message role="status" hidden></div></section></div>`,
    '/invoice/1/cetak': `<article class="receipt"><div class="receipt-head"><div class="brand"><span class="brand-mark">IA</span><span>INNES ARSEN<small>WISATA &amp; TRAVEL</small></span></div><div class="receipt-title"><span class="eyebrow">DEMO TAGIHAN</span><h1>INV-DEMO-001</h1></div></div><hr><div class="receipt-columns"><div><span class="muted small">DITERBITKAN UNTUK</span><h3>Pelanggan Demo</h3></div><div><span class="muted small">STATUS</span><h3>SIMULASI</h3></div></div><div class="receipt-total"><span>Total</span><strong>Rp 1.343.001</strong></div><div class="notice">Dokumen ini adalah simulasi tampilan dan bukan bukti pembayaran sah.</div><div class="receipt-actions no-print"><button class="button" type="button" onclick="window.print()">Cetak / Simpan PDF</button><a class="button button-light" href="/invoice/1">Kembali ke invoice</a></div></article>`,
    '/admin': `<section class="page-heading"><span class="eyebrow">Demo panel</span><h1>Dashboard admin</h1><p>Simulasi tampilan pengelolaan PT. Innes Arsen Wisata.</p></section><div class="notice">Data dan aksi admin di halaman Vercel ini bersifat demo. Operasi riil membutuhkan backend Laravel dan database MySQL.</div><div class="stats-grid"><article class="stat-card"><span>Paket</span><strong>3</strong><a href="/admin/paket">Kelola paket</a></article><article class="stat-card"><span>Pesanan</span><strong>1</strong><a href="/admin/pesanan">Kelola pesanan</a></article><article class="stat-card"><span>Pembayaran</span><strong>1</strong><a href="/admin/pembayaran">Review pembayaran</a></article><article class="stat-card"><span>Konsumen</span><strong>1</strong><a href="/admin/konsumen">Lihat konsumen</a></article><article class="stat-card"><span>MoU</span><strong>0</strong><a href="/admin/mou">Kelola MoU</a></article></div>`,
    '/admin/paket': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola paket</h1></section><div class="form-card"><div class="notice">Tampilan demo daftar paket. Perubahan paket hanya tersedia pada backend Laravel.</div><div class="admin-list"><div class="admin-item"><summary>Paket Wisata Sidoarjo</summary></div><div class="admin-item"><summary>Paket Wisata Dieng</summary></div><div class="admin-item"><summary>Jelajah Lombok</summary></div></div><button class="button" type="button" data-demo-action>Simulasi tambah paket</button><div class="alert alert-success" data-demo-action-message role="status" hidden></div></div>`,
    '/admin/wisata': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola wisata</h1></section><div class="form-card"><div class="notice">Tampilan demo destinasi wisata. Pengelolaan data dilakukan oleh backend Laravel.</div><div class="admin-list"><div class="admin-item"><summary>Sidoarjo</summary></div><div class="admin-item"><summary>Dieng</summary></div><div class="admin-item"><summary>Lombok</summary></div></div><button class="button" type="button" data-demo-action>Simulasi tambah wisata</button><div class="alert alert-success" data-demo-action-message role="status" hidden></div></div>`,
    '/admin/pesanan': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola pesanan</h1></section><div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Pelanggan</th><th>Paket wisata</th><th>Status</th><th>Aksi</th></tr></thead><tbody><tr><td><a class="text-link" href="/invoice/1">INV-DEMO-001</a></td><td>Pelanggan Demo</td><td><a class="text-link" href="/paket/sidoarjo">Paket Wisata Sidoarjo</a></td><td><a class="badge" href="/invoice/1" data-demo-payment-status>Menunggu pembayaran</a></td><td><a class="text-link" href="/invoice/1">Buka detail</a></td></tr></tbody></table></div>`,
    '/admin/pembayaran': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola pembayaran</h1></section><div class="form-card"><div class="notice">Data pembayaran nyata tidak tersedia pada situs statis Vercel. Tombol berikut hanya mengubah status demo di browser ini.</div><div class="payment-review"><div><strong>INV-DEMO-001</strong><small>Pelanggan Demo · Rp 1.343.001 · Status: <span data-demo-payment-status>Menunggu pembayaran</span></small></div><button class="button button-small" type="button" data-demo-payment-review>Simulasikan verifikasi diterima</button></div><div class="alert alert-success" data-demo-payment-message role="status" hidden></div><a class="button button-light" href="/invoice/1">Buka detail invoice</a></div>`,
    '/admin/konsumen': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola konsumen</h1></section><div class="form-card"><div class="notice">Daftar konsumen hanya tersedia pada backend Laravel dengan autentikasi admin.</div><div class="admin-item"><summary>Pelanggan Demo · customer@demo.test</summary></div></div>`,
    '/admin/mou': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Kelola MoU</h1></section><div class="form-card"><div class="notice">Belum ada pengajuan MoU pada data demo. Pengajuan nyata tersedia pada backend Laravel.</div><a class="button" href="/mou">Buka simulasi MoU</a></div>`,
    '/admin/pengaturan': `<section class="page-heading"><span class="eyebrow">Demo admin</span><h1>Pengaturan</h1></section><div class="form-card form-stack"><div class="notice">Pengaturan perusahaan dan metode pembayaran merupakan data backend. Situs Vercel tidak menyimpan data atau kredensial.</div><label>Nama perusahaan<input type="text" value="PT. Innes Arsen Wisata" disabled></label><button class="button" type="button" data-demo-action>Simpan simulasi</button><div class="alert alert-success" data-demo-action-message role="status" hidden></div></div>`,
};

for (const item of packages) {
    const href = `/paket/${item.slug}`;
    routes.push([`paket-${item.slug}`, href, item.name]);
    pageBodies[href] = `<div class="breadcrumb"><a href="/destinasi">Paket wisata</a> / ${item.name}</div><div class="detail-grid"><article class="detail-main"><div class="package-art package-art-large has-image"><img class="package-art-image" src="${item.image}" alt="${item.place}"><span>${item.category}</span></div><span class="eyebrow">${item.place}</span><h1>${item.name}</h1><div class="package-meta">${item.duration}</div><h2>Ringkasan perjalanan</h2><p class="long-copy">Nikmati perjalanan terencana bersama Innes Arsen Wisata. Rincian jadwal dan fasilitas akan dikonfirmasi sebelum pemesanan.</p><a class="button button-light" href="/destinasi">Kembali ke semua paket</a></article><aside class="booking-card"><span class="eyebrow">Harga mulai dari</span><div class="price-large">${item.price}<small> / orang</small></div><p>Pemesanan riil tersedia setelah masuk ke backend aplikasi.</p><a class="button button-block" href="/masuk">Masuk untuk memesan</a></aside></div>`;
}

for (const [, href, label] of routes) {
    const relativePath = href === '/' ? 'index.html' : `${href.slice(1)}.html`;
    const outputPath = path.join(dist, relativePath);
    await mkdir(path.dirname(outputPath), { recursive: true });
    await writeFile(outputPath, page(label, href, pageBodies[href] ?? pageBodies['/']));
}

await writeFile(
    path.join(dist, 'masuk.html'),
    page('Masuk', '/masuk', pageBodies['/masuk']),
);

console.log(`Static site generated in ${dist}`);
