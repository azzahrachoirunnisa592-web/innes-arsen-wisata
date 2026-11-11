@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<section class="auth-wrap"><div class="auth-intro"><span class="eyebrow">Mulai perjalanan Anda</span><h1>Buat akun konsumen.</h1><p>Daftar untuk memesan paket wisata, memantau pembayaran, serta mengajukan kerja sama MoU.</p></div>
    <form class="form-card form-stack" method="post" action="{{ route('register.store') }}">@csrf
        <h2>Registrasi</h2><label>Nama lengkap<input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Nomor telepon<input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"></label>
        <label>Kata sandi<input type="password" name="password" minlength="8" autocomplete="new-password" required><small>Minimal 8 karakter.</small></label>
        <label>Ulangi kata sandi<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="button button-block" type="submit">Buat akun</button><p class="form-foot">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a></p>
    </form>
</section>
@endsection
