@extends('layouts.app')
@section('title', 'Kerja sama MoU')
@section('content')
<section class="page-heading"><span class="eyebrow">Kemitraan</span><h1>Ajukan kerja sama MoU</h1><p>Sampaikan informasi organisasi dan rencana kerja sama. Tim kami akan menghubungi Anda untuk tindak lanjut.</p></section>
<div class="mou-layout">
    <form class="panel form-stack" method="post" action="{{ route('mous.store') }}" enctype="multipart/form-data">@csrf
        <h2>Formulir pengajuan</h2>
        <label>Nama organisasi / instansi<input name="organization_name" value="{{ old('organization_name') }}" maxlength="180" required></label>
        <label>Nama narahubung<input name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" maxlength="120" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required></label>
        <label>Nomor telepon<input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" maxlength="30" required></label>
        <label>Rencana dan kebutuhan kerja sama<textarea name="description" rows="5" maxlength="5000" required>{{ old('description') }}</textarea></label>
        <label>Dokumen pendukung (PDF/DOC/DOCX; maks. 10 MB)<input type="file" name="document" accept=".pdf,.doc,.docx"></label>
        <button class="button" type="submit">Kirim pengajuan MoU</button>
    </form>
    <section class="panel"><h2>Riwayat pengajuan</h2>
        @forelse($mous as $mou)<article class="list-item"><div class="list-item-head"><strong>{{ $mou->organization_name }}</strong><span class="badge">{{ str_replace('_', ' ', $mou->status) }}</span></div><p>{{ $mou->created_at->format('d M Y') }} · {{ $mou->contact_name }}</p><p>{{ $mou->admin_notes ?: 'Menunggu tindak lanjut tim kami.' }}</p>
            @if($mou->document_path)<a class="text-link" href="{{ route('documents.mou', [$mou, 'customer']) }}">Unduh dokumen Anda</a>@endif
            @if($mou->admin_document_path)<a class="text-link" href="{{ route('documents.mou', [$mou, 'company']) }}">Unduh dokumen dari perusahaan</a>@endif
        </article>@empty<p class="muted">Belum ada pengajuan.</p>@endforelse
    </section>
</div>
@endsection
