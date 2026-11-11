@extends('layouts.app')
@section('title', 'Kelola paket wisata')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Data master</span><h1>Kelola paket wisata</h1><p>Atur harga, kategori, kuota, dan publikasi paket.</p></section>
<section class="panel form-panel"><h2>Tambah paket wisata</h2>
    @if($wisatas->isEmpty())<div class="notice">Buat destinasi wisata terlebih dahulu sebelum menambahkan paket.</div>@else
    <form method="post" action="{{ route('admin.packages.store') }}" class="form-grid">@csrf
        <label>Destinasi<select name="wisata_id" required>@foreach($wisatas as $wisata)<option value="{{ $wisata->id }}">{{ $wisata->name }}</option>@endforeach</select></label>
        <label>Nama paket<input name="title" value="{{ old('title') }}" maxlength="180" required></label>
        <label>Kategori<select name="category" required><option value="">Pilih kategori</option>@foreach(config('travel.package_categories') as $category)<option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></label>
        <label>Harga per orang (Rp)<input type="number" name="price" min="1" step="1000" required></label>
        <label>Durasi (hari)<input type="number" name="duration_days" min="1" value="1" required></label>
        <label>Durasi (malam)<input type="number" name="duration_nights" min="0" value="0" required></label>
        <label>Kuota peserta<input type="number" name="quota" min="1" value="20" required></label>
        <label class="span-2">Deskripsi<textarea name="description" rows="3" required></textarea></label>
        <label class="span-2">Termasuk<textarea name="includes" rows="2"></textarea></label>
        <label class="span-2">Tidak termasuk<textarea name="excludes" rows="2"></textarea></label>
        <label class="check-row span-2"><input type="checkbox" name="published" value="1"> Publikasikan paket</label>
        <div class="span-2"><button class="button" type="submit">Simpan paket</button></div>
    </form>@endif
</section>
<section class="section"><h2>Daftar paket</h2>
    <div class="admin-list">@forelse($packages as $paket)
        <details class="admin-item"><summary><span><strong>{{ $paket->title }}</strong><small>{{ $paket->wisata->name }} · {{ $paket->category }} · Rp {{ number_format($paket->price, 0, ',', '.') }}</small></span><span class="badge">{{ $paket->published ? 'dipublikasikan' : 'draft' }}</span></summary>
            <form method="post" action="{{ route('admin.packages.update', $paket) }}" class="form-grid admin-edit">@csrf @method('PUT')
                <label>Destinasi<select name="wisata_id">@foreach($wisatas as $wisata)<option value="{{ $wisata->id }}" @selected($paket->wisata_id === $wisata->id)>{{ $wisata->name }}</option>@endforeach</select></label>
                <label>Nama paket<input name="title" value="{{ $paket->title }}" required></label><label>Kategori<select name="category" required>@foreach(config('travel.package_categories') as $category)<option value="{{ $category }}" @selected($paket->category === $category)>{{ $category }}</option>@endforeach</select></label>
                <label>Harga per orang<input type="number" name="price" min="1" step="1000" value="{{ $paket->price }}" required></label>
                <label>Durasi (hari)<input type="number" name="duration_days" min="1" value="{{ $paket->duration_days }}" required></label><label>Durasi (malam)<input type="number" name="duration_nights" min="0" value="{{ $paket->duration_nights }}" required></label>
                <label>Kuota<input type="number" name="quota" min="1" value="{{ $paket->quota }}" required></label>
                <label class="span-2">Deskripsi<textarea name="description" rows="3" required>{{ $paket->description }}</textarea></label>
                <label class="span-2">Termasuk<textarea name="includes" rows="2">{{ $paket->includes }}</textarea></label><label class="span-2">Tidak termasuk<textarea name="excludes" rows="2">{{ $paket->excludes }}</textarea></label>
                <label class="check-row span-2"><input type="checkbox" name="published" value="1" @checked($paket->published)> Dipublikasikan</label>
                <div class="span-2 action-row"><button class="button button-small" type="submit">Simpan perubahan</button></div>
            </form>
            <form method="post" action="{{ route('admin.packages.delete', $paket) }}" class="delete-form" onsubmit="return confirm('Hapus paket ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus paket</button></form>
        </details>
    @empty<div class="empty-state">Belum ada paket wisata.</div>@endforelse</div>
</section>
@endsection
