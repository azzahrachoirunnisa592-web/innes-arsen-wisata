@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<section class="auth-wrap"><div class="auth-intro"><span class="eyebrow">Selamat datang kembali</span><h1>Masuk ke akun Anda.</h1><p>Lihat status pesanan, lanjutkan pembayaran, atau pantau pengajuan kerja sama Anda.</p></div>
    <form class="form-card form-stack" method="post" action="{{ route('login.store') }}">@csrf
        <h2>Masuk</h2><label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <label>Kata sandi<input type="password" name="password" autocomplete="current-password" required></label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="button button-block" type="submit">Masuk ke akun</button><p class="form-foot">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </form>
</section>
@endsection
