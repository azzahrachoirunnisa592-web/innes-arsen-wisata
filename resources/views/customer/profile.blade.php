@extends('layouts.app')
@section('title', 'Profil pelanggan')
@section('content')
<section class="page-heading page-heading-row">
    <div><span class="eyebrow">Akun pelanggan</span><h1>Profil saya</h1><p>Perbarui informasi kontak dan kata sandi akun Anda.</p></div>
    <a class="button button-light" href="{{ route('customer.dashboard') }}">Kembali ke pesanan</a>
</section>
<section class="panel form-panel">
    <form method="post" action="{{ route('customer.profile.update') }}" class="form-grid">
        @csrf @method('PUT')
        <label>Nama lengkap<input name="name" value="{{ old('name', $customer->name) }}" maxlength="120" required autocomplete="name"></label>
        <label>Email<input type="email" name="email" value="{{ old('email', $customer->email) }}" maxlength="255" required autocomplete="email"></label>
        <label>Nomor telepon<input name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="30" autocomplete="tel"></label>
        <div class="span-2"><h2>Ganti kata sandi (opsional)</h2><p>Kosongkan jika tidak ingin mengganti kata sandi.</p></div>
        <label>Kata sandi saat ini<input type="password" name="current_password" autocomplete="current-password"></label>
        <label>Kata sandi baru<input type="password" name="password" minlength="8" autocomplete="new-password"></label>
        <label>Konfirmasi kata sandi baru<input type="password" name="password_confirmation" minlength="8" autocomplete="new-password"></label>
        <div class="span-2"><button class="button" type="submit">Simpan profil</button></div>
    </form>
</section>
@endsection
