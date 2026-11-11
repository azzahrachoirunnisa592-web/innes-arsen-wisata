@extends('layouts.app')
@section('title', 'Profil & pembayaran')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Konfigurasi</span><h1>Profil perusahaan & pembayaran</h1><p>Informasi ini ditampilkan kepada pengunjung dan pelanggan. Pastikan data tujuan pembayaran resmi.</p></section>
<section class="panel form-panel"><h2>Profil perusahaan</h2>
    <form method="post" action="{{ route('admin.profile.update') }}" class="form-grid">@csrf @method('PUT')
        <label>Nama perusahaan<input name="name" value="{{ $profile->name }}" required maxlength="180"></label><label>Telepon<input name="phone" value="{{ $profile->phone }}" maxlength="30"></label>
        <label>Email<input type="email" name="email" value="{{ $profile->email }}"></label><label>Website<input type="url" name="website" value="{{ $profile->website }}" placeholder="https://"></label>
        <label class="span-2">Alamat<textarea name="address" rows="2">{{ $profile->address ?: config('profile.address') }}</textarea></label><label class="span-2">Deskripsi profil<textarea name="description" rows="4">{{ $profile->description }}</textarea></label>
        <div class="span-2"><button class="button" type="submit">Simpan profil</button></div>
    </form>
</section>
<section class="panel form-panel"><h2>Galeri dokumentasi perusahaan</h2>
    <p class="muted small">Unggah foto asli kegiatan kantor atau perjalanan wisata perusahaan. Format JPG, PNG, atau WebP, maksimal 5 MB per foto.</p>
    <form method="post" action="{{ route('admin.gallery.store') }}" class="form-grid" enctype="multipart/form-data">@csrf
        <label>Judul foto<input name="title" maxlength="180" required></label>
        <label>Urutan tampilan<input type="number" name="sort_order" min="0" max="65535" value="0"></label>
        <label class="span-2">Keterangan<input name="caption" maxlength="500"></label>
        <label class="span-2">Berkas foto<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required></label>
        <label class="check-row span-2"><input type="checkbox" name="published" value="1" checked> Tampilkan di halaman Profil</label>
        <div class="span-2"><button class="button button-small" type="submit">Unggah ke galeri</button></div>
    </form>
    @forelse($galleryImages as $galleryImage)
        <details class="admin-item">
            <summary><span><strong>{{ $galleryImage->title }}</strong><small>{{ $galleryImage->caption ?: 'Tanpa keterangan' }}</small></span><span class="badge">{{ $galleryImage->published ? 'ditampilkan' : 'disembunyikan' }}</span></summary>
            <form method="post" action="{{ route('admin.gallery.update', $galleryImage) }}" class="form-grid admin-edit" enctype="multipart/form-data">@csrf @method('PUT')
                <label>Judul foto<input name="title" value="{{ $galleryImage->title }}" maxlength="180" required></label>
                <label>Urutan tampilan<input type="number" name="sort_order" min="0" max="65535" value="{{ $galleryImage->sort_order }}"></label>
                <label class="span-2">Keterangan<input name="caption" value="{{ $galleryImage->caption }}" maxlength="500"></label>
                <div class="span-2 small"><img src="{{ Storage::disk('public')->url($galleryImage->image_path) }}" alt="{{ $galleryImage->caption ?: $galleryImage->title }}" width="180" loading="lazy"></div>
                <label class="span-2">Ganti foto (opsional)<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
                <label class="check-row span-2"><input type="checkbox" name="published" value="1" @checked($galleryImage->published)> Tampilkan di halaman Profil</label>
                <div class="span-2 action-row"><button class="button button-small" type="submit">Simpan foto</button></div>
            </form>
            <form method="post" action="{{ route('admin.gallery.delete', $galleryImage) }}" class="delete-form" onsubmit="return confirm('Hapus foto dokumentasi ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus foto</button></form>
        </details>
    @empty
        <p class="muted small">Belum ada foto dokumentasi. Foto akan tampil di halaman Profil setelah diunggah dan dipublikasikan.</p>
    @endforelse
</section>
<section class="panel form-panel"><h2>Struktur manajemen</h2>
    <form method="post" action="{{ route('admin.managers.store') }}" class="form-grid">@csrf
        <label>Nama<input name="name" required></label><label>Jabatan<input name="position" required></label><label>Urutan tampilan<input type="number" name="sort_order" min="0" value="0"></label><label>Biografi singkat<input name="bio"></label><div class="span-2"><button class="button button-small" type="submit">Tambah pengurus</button></div>
    </form>
    @foreach($managers as $manager)<details class="admin-item"><summary><span><strong>{{ $manager->name }}</strong><small>{{ $manager->position }}</small></span><span class="badge">Manajemen</span></summary>
        <form method="post" action="{{ route('admin.managers.update', $manager) }}" class="form-grid admin-edit">@csrf @method('PUT')
            <label>Nama<input name="name" value="{{ $manager->name }}" required></label><label>Jabatan<input name="position" value="{{ $manager->position }}" required></label>
            <label>Urutan tampilan<input type="number" name="sort_order" min="0" value="{{ $manager->sort_order }}"></label><label>Biografi singkat<input name="bio" value="{{ $manager->bio }}"></label>
            <div class="span-2 action-row"><button class="button button-small" type="submit">Simpan perubahan</button></div>
        </form>
        <form method="post" action="{{ route('admin.managers.delete', $manager) }}" class="delete-form" onsubmit="return confirm('Hapus data manajemen ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus</button></form>
    </details>@endforeach
</section>
<section class="panel form-panel"><h2>Metode pembayaran & akun tujuan</h2><p class="muted small">QR DANA berasal dari berkas <code>public/images/qris-dana.png</code>. Nomor yang tersedia saat ini tersamarkan (62-896****8728); minta admin memastikan data penerima resmi sebelum pelanggan membayar.</p>
    @if(file_exists(public_path('images/qris-dana.png')))<img class="admin-qris-preview" src="{{ asset('images/qris-dana.png') }}" alt="QR pembayaran DANA" loading="lazy">@else<div class="notice">Berkas QR belum tersedia di <code>public/images/qris-dana.png</code>.</div>@endif
    <form method="post" action="{{ route('admin.payment-methods.store') }}" class="form-grid">@csrf
        <label>Nama metode<input name="name" placeholder="QRIS DANA / Transfer Bank" required></label><label>Nama pemilik<input name="account_name"></label>
        <label>Nomor akun tujuan<input name="account_number"></label><label class="span-2">Petunjuk pembayaran<textarea name="instructions" rows="2" maxlength="2000"></textarea></label>
        <label class="check-row span-2"><input type="checkbox" name="active" value="1" checked> Aktif</label><div class="span-2"><button class="button button-small" type="submit">Tambah metode pembayaran</button></div>
    </form>
    @foreach($paymentMethods as $method)<details class="admin-item"><summary><span><strong>{{ $method->name }}</strong><small>{{ $method->account_name }} · {{ $method->account_number }}</small></span><span class="badge">{{ $method->active ? 'aktif' : 'nonaktif' }}</span></summary>
        <form method="post" action="{{ route('admin.payment-methods.update', $method) }}" class="form-grid admin-edit">@csrf @method('PUT')
            <label>Nama metode<input name="name" value="{{ $method->name }}" required></label><label>Nama pemilik<input name="account_name" value="{{ $method->account_name }}"></label><label>Nomor akun<input name="account_number" value="{{ $method->account_number }}"></label><label class="span-2">Petunjuk pembayaran<textarea name="instructions" rows="2" maxlength="2000">{{ $method->instructions }}</textarea></label>
            <label class="check-row span-2"><input type="checkbox" name="active" value="1" @checked($method->active)> Aktif</label><div class="span-2 action-row"><button class="button button-small" type="submit">Simpan perubahan</button></div>
        </form>
        <form method="post" action="{{ route('admin.payment-methods.delete', $method) }}" class="delete-form" onsubmit="return confirm('Hapus metode pembayaran ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus metode</button></form>
    </details>@endforeach
</section>
@endsection
