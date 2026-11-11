@extends('layouts.app')
@section('title', 'Kelola destinasi')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Data master</span><h1>Destinasi wisata</h1><p>Kelola data destinasi dan daerah tujuan perjalanan.</p></section>
<section class="panel form-panel"><h2>Tambah destinasi</h2><form method="post" action="{{ route('admin.destinations.store') }}" class="form-grid" enctype="multipart/form-data">@csrf
    <label>Nama destinasi<input name="name" maxlength="180" required></label><label>Provinsi<input name="province" maxlength="100"></label><label class="span-2">Deskripsi<textarea name="description" rows="3"></textarea></label>
    <label class="span-2">Foto destinasi (JPG, PNG, atau WebP; maks. 5 MB)<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label><div class="span-2"><button class="button" type="submit">Simpan destinasi</button></div>
</form></section>
<section class="section"><h2>Daftar destinasi</h2><div class="admin-list">
@forelse($wisatas as $wisata)<details class="admin-item"><summary><span><strong>{{ $wisata->name }}</strong><small>{{ $wisata->province }} · {{ $wisata->packages_count }} paket</small></span><span class="badge">Destinasi</span></summary>
    <form method="post" action="{{ route('admin.destinations.update', $wisata) }}" class="form-grid admin-edit" enctype="multipart/form-data">@csrf @method('PUT')
        <label>Nama destinasi<input name="name" value="{{ $wisata->name }}" required></label><label>Provinsi<input name="province" value="{{ $wisata->province }}"></label><label class="span-2">Deskripsi<textarea name="description" rows="3">{{ $wisata->description }}</textarea></label>
        @if($wisata->image_path)<div class="span-2"><img class="admin-destination-image" src="{{ $wisata->image_url }}" alt="Foto destinasi {{ $wisata->name }}" loading="lazy"></div>@endif
        <label class="span-2">Ganti foto destinasi (opsional; JPG, PNG, atau WebP, maks. 5 MB)<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label><div class="span-2"><button class="button button-small" type="submit">Simpan perubahan</button></div>
    </form>
    <form method="post" action="{{ route('admin.destinations.delete', $wisata) }}" class="delete-form" onsubmit="return confirm('Hapus destinasi ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus destinasi</button></form>
</details>@empty<div class="empty-state">Belum ada destinasi.</div>@endforelse
</div></section>
@endsection
