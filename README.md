# Sistem Informasi Pelayanan Jasa Tour dan Travel

Aplikasi Laravel untuk PT. Innes Arsen Wisata dengan role admin/konsumen, katalog paket dan destinasi, pemesanan, negosiasi, invoice, konfirmasi pembayaran manual QRIS/transfer, serta pengajuan dokumen kerja sama MoU.

## Persyaratan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dan `gd`.
- Composer 2.
- MySQL 8.0+ atau MariaDB yang kompatibel.

## Instalasi lokal (Windows PowerShell)

1. Buka XAMPP Control Panel dan nyalakan **MySQL** (port `3306`). Konfigurasi `.env` proyek ini menggunakan database `innes_arsen_wisata`, host `127.0.0.1`, port `3306`, dan user `root` tanpa kata sandi—sesuai konfigurasi standar XAMPP lokal. Jika instalasi MySQL Anda memakai kata sandi, isi `DB_PASSWORD` di `.env` dengan kata sandi lokal tersebut.

2. Buat database bila belum ada:

   ```sql
   CREATE DATABASE innes_arsen_wisata CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

Database sudah dibuatkan oleh proyek pada sesi setup ini. Untuk instalasi baru, salin `.env.example` menjadi `.env`, lalu sesuaikan kredensial MySQL, URL aplikasi, dan kunci admin awal:

   ```powershell
   Copy-Item .env.example .env
   ```

   Isi `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dengan kredensial awal yang kuat sebelum menjalankan seeder. Seeder hanya membuat akun admin jika kedua variabel itu terisi.

3. Pasang dependency dan inisialisasi:

   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

4. Buka URL yang ditampilkan `artisan serve`. Jalankan pengujian dengan `php artisan test`.

## Navigasi halaman publik

Menu publik memiliki halaman tersendiri: **Beranda**, **Destinasi**, **Jelajah**, **Budaya & Sejarah**, **Kuliner**, **Profil**, **Advertorial**, dan **Berita**. Daftar destinasi dan profil perusahaan mengikuti data database; artikel editorial ditata pada `config/editorial.php` dan dapat dibuka melalui halaman kategori maupun halaman detail artikel. Navigasi menggunakan komponen responsif Bootstrap dan tata letak grid yang menyesuaikan layar ponsel maupun komputer.

Konten sejarah, visi, misi, daftar layanan, alamat, dan tautan Instagram halaman Profil dikelola di `config/profile.php`. Artikel pada panel **Artikel terbaru** mengikuti tiga entri teratas kategori Berita di `config/editorial.php`. Foto dokumentasi profil dapat diunggah admin melalui **Panel admin → Profil & pembayaran → Galeri dokumentasi perusahaan**; hanya foto yang dipublikasikan tampil kepada pengunjung. Jalankan `php artisan storage:link` agar foto pada disk publik dapat diakses melalui web. Kontak dan struktur manajemen tetap mengikuti data yang dikelola melalui panel admin.

Seluruh rute `/admin/*` dilindungi alias middleware `admin` di `bootstrap/app.php`. Hanya akun terautentikasi dengan kolom `role` bernilai `admin` yang dapat mengaksesnya; tamu dan akun non-admin diarahkan kembali ke Beranda. Login tetap menggunakan formulir `/masuk`; akun admin yang terdaftar diarahkan ke dashboard admin.

URL `/login` juga membuka formulir masuk yang sama untuk kompatibilitas dengan tautan Laravel standar. Untuk membuat akun admin awal, isi `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`, lalu jalankan `php artisan db:seed`. Jangan gunakan kredensial bawaan atau membagikan kata sandi admin.

Kode QR DANA yang dilampirkan disimpan di `public/images/qris-dana.png` dan ditampilkan pada halaman pembayaran. Seeder menambahkan pilihan `DANA QR`. Kode dari lampiran tampak sebagai QR profil DANA (bukan QRIS dinamis); pelanggan perlu memeriksa nama penerima dan nominal pada aplikasi DANA sebelum mengirim uang. Admin tetap harus mencocokkan pembayaran secara manual dengan rekening penerima sebelum menyetujui bukti. Jika metode pembayaran belum tampil pada basis data yang sudah ada, jalankan `php artisan db:seed` atau tambahkan metode tersebut melalui **Panel admin → Profil & pembayaran**. Aplikasi tidak membuat QR atau memverifikasi transfer otomatis.

## Hak akses dan alur transaksi

- Pendaftaran publik selalu membuat role `customer`; role tidak dapat dipilih dari formulir.
- Admin awal dibuat melalui variabel lingkungan saat seeding. Batasi kredensial tersebut dan ganti jika sudah tidak diperlukan.
- Konsumen memilih paket dan mengajukan tanggal/jumlah peserta. Pesanan dan invoice dibuat dalam satu transaksi database.
- Admin menetapkan hasil negosiasi dan nominal final. Pembayaran dibuka setelah status negosiasi disetujui.
- Konsumen mengunggah bukti pembayaran. File bukti dan dokumen MoU disimpan pada disk privat dan hanya bisa diunduh oleh pemilik atau admin.
- Admin memeriksa bukti secara manual. Pembayaran yang diterima mengonfirmasi pesanan dan menjadikan invoice kuitansi; pembayaran yang ditolak dapat diajukan kembali.
- Pengajuan MoU dapat ditinjau admin, diberi catatan/status, dan dilengkapi dokumen balasan.

## Peta struktur utama

```text
app/
  Http/Controllers/       Auth, katalog, pemesanan, pembayaran, MoU, admin, dokumen
  Http/Middleware/        Pembatasan role
  Models/                 User, Paket, Wisata, Booking, Invoice, Payment, MoU, profil
database/
  migrations/             Tabel pengguna dan seluruh entitas layanan
  seeders/                Profil awal, destinasi/paket contoh, admin opsional
resources/views/          Halaman publik, pelanggan, dan panel admin (Blade)
public/css/app.css        Gaya responsif
public/images/            Tempat QRIS merchant resmi
routes/web.php            Rute web dan pembatasan akses
tests/Feature/            Pengujian halaman, registrasi, pemesanan, role
```

## Status yang dipakai

- Booking: `pending_negotiation`, `awaiting_payment`, `payment_review`, `confirmed`, `cancelled`.
- Invoice: `pending`, `submitted`, `paid`.
- Pembayaran: `submitted`, `paid`, `rejected`.
- MoU: `submitted`, `under_review`, `approved`, `rejected`.

## Diagram sistem

Diagram SVG dapat dibuka langsung di browser dan dicetak:

- [Flowchart pemesanan paket wisata dan pembayaran](./public/images/flowchart-pemesanan.svg)
- [Flowchart pengajuan kerja sama MoU](./public/images/flowchart-kerja-sama-mou.svg)
- [ERD sistem dan relasi tabel](./public/images/erd-sistem.svg)
- [DFD sistem (level 0 dan level 1)](./public/images/dfd-sistem.svg)
- [Use case diagram sistem](./public/images/use-case-sistem.svg)

Sebelum produksi, atur HTTPS, backup database, kebijakan retensi dokumen, kredensial email, serta proses rekonsiliasi pembayaran sesuai rekening perusahaan. Pengujian black-box dapat mengikuti formulir registrasi, pemesanan, negosiasi admin, unggah bukti, review pembayaran, cetak invoice, dan pengajuan/review MoU.
