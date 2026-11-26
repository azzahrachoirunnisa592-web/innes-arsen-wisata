@extends('layouts.app')
@section('title', 'Kelola konten')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Konten situs</span><h1>Kelola berita dan artikel</h1><p>Konten tersimpan di database. Draft tidak tampil kepada pengunjung.</p></section>
<section class="panel form-panel">
    <h2>Tambah konten</h2>
    <form method="post" action="{{ route('admin.editorial.store') }}" class="form-grid">@csrf
        <label>Bagian<select name="section" required>@foreach($sections as $key => $section)<option value="{{ $key }}" @selected(old('section') === $key)>{{ $section['name'] }}</option>@endforeach</select></label>
        <label>Slug URL<input name="slug" value="{{ old('slug') }}" maxlength="180" required placeholder="judul-artikel"></label>
        <label class="span-2">Judul<input name="title" value="{{ old('title') }}" maxlength="180" required></label>
        <label class="span-2">Ringkasan<textarea name="summary" rows="2" maxlength="500" required>{{ old('summary') }}</textarea></label>
        <label class="span-2">URL gambar<input type="url" name="image" value="{{ old('image') }}" maxlength="2048" required placeholder="https://..."></label>
        <label class="span-2">Teks alternatif gambar<input name="alt" value="{{ old('alt') }}" maxlength="255" required></label>
        <label class="span-2">Isi artikel<textarea name="body" rows="8" required placeholder="Pisahkan paragraf dengan satu baris kosong.">{{ old('body') }}</textarea></label>
        <label class="check-row span-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published'))> Publikasikan</label>
        <div class="span-2"><button class="button" type="submit">Simpan konten</button></div>
    </form>
</section>
<section class="section"><h2>Daftar konten</h2>
    <div class="admin-list">@forelse($articles as $article)
        <details class="admin-item"><summary><span><strong>{{ $article->title }}</strong><small>{{ $sections[$article->section]['name'] ?? $article->section }} · /artikel/{{ $article->slug }}</small></span><span class="badge">{{ $article->is_published ? 'dipublikasikan' : 'draft' }}</span></summary>
            <form method="post" action="{{ route('admin.editorial.update', $article) }}" class="form-grid admin-edit">@csrf @method('PUT')
                <label>Bagian<select name="section" required>@foreach($sections as $key => $section)<option value="{{ $key }}" @selected($article->section === $key)>{{ $section['name'] }}</option>@endforeach</select></label>
                <label>Slug URL<input name="slug" value="{{ $article->slug }}" maxlength="180" required></label>
                <label class="span-2">Judul<input name="title" value="{{ $article->title }}" maxlength="180" required></label>
                <label class="span-2">Ringkasan<textarea name="summary" rows="2" maxlength="500" required>{{ $article->summary }}</textarea></label>
                <label class="span-2">URL gambar<input type="url" name="image" value="{{ $article->image }}" maxlength="2048" required></label>
                <label class="span-2">Teks alternatif gambar<input name="alt" value="{{ $article->alt }}" maxlength="255" required></label>
                <label class="span-2">Isi artikel<textarea name="body" rows="8" required>{{ implode("\n\n", $article->body ?? []) }}</textarea></label>
                <label class="check-row span-2"><input type="checkbox" name="is_published" value="1" @checked($article->is_published)> Dipublikasikan</label>
                <div class="span-2"><button class="button button-small" type="submit">Simpan perubahan</button></div>
            </form>
            <form method="post" action="{{ route('admin.editorial.delete', $article) }}" class="delete-form" onsubmit="return confirm('Hapus konten ini?')">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit">Hapus konten</button></form>
        </details>
    @empty<div class="empty-state">Belum ada konten.</div>@endforelse</div>
</section>
@endsection
