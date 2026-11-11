@extends('layouts.app')
@section('title', 'Kelola MoU')
@section('content')
@include('admin._nav')
<section class="page-heading"><span class="eyebrow">Kemitraan</span><h1>Pengajuan kerja sama MoU</h1><p>Tinjau permintaan kerja sama dan kirim dokumen hasil tindak lanjut.</p></section>
<div class="admin-list">
@forelse($mous as $mou)<details class="admin-item" @if($loop->first) open @endif>
    <summary><span><strong>{{ $mou->organization_name }}</strong><small>{{ $mou->contact_name }} · {{ $mou->email }} · {{ $mou->created_at->format('d M Y') }}</small></span><span class="badge">{{ str_replace('_', ' ', $mou->status) }}</span></summary>
    <div class="admin-item-content"><p>{{ $mou->description }}</p><div class="summary-row"><span>Telepon</span><strong>{{ $mou->phone }}</strong></div>
        @if($mou->document_path)<a class="text-link" href="{{ route('documents.mou', [$mou, 'customer']) }}">Unduh dokumen pengaju</a>@endif
        @if($mou->admin_document_path)<a class="text-link" href="{{ route('documents.mou', [$mou, 'company']) }}">Unduh dokumen perusahaan</a>@endif
        <form method="post" action="{{ route('admin.mous.review', $mou) }}" enctype="multipart/form-data" class="form-grid admin-edit">@csrf @method('PUT')
            <label>Status<select name="status"><option value="under_review" @selected($mou->status === 'under_review')>Sedang ditinjau</option><option value="approved" @selected($mou->status === 'approved')>Disetujui</option><option value="rejected" @selected($mou->status === 'rejected')>Ditolak</option></select></label>
            <label>Dokumen balasan (PDF/DOC/DOCX)<input type="file" name="admin_document" accept=".pdf,.doc,.docx"></label>
            <label class="span-2">Catatan admin<textarea name="admin_notes" rows="3">{{ $mou->admin_notes }}</textarea></label><div class="span-2"><button class="button button-small" type="submit">Simpan tindak lanjut</button></div>
        </form>
    </div>
</details>@empty<div class="empty-state">Belum ada pengajuan MoU.</div>@endforelse
</div>
@endsection
