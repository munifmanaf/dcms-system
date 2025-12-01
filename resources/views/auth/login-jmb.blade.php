@extends('layouts.app')

@section('title', 'Log Masuk - Sistem Koleksi Digital JMB')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <img src="{{ asset('images/jmb-logo.png') }}" alt="Jabatan Mufti Brunei">
        <br>
        <a href="#"><b>Sistem Koleksi Digital</b><br>Jabatan Mufti Brunei</a>
    </div>
    
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sila log masuk untuk akses sistem</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Kata Laluan" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ingat Saya</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Log Masuk</button>
                    </div>
                </div>
            </form>

            <p class="mb-1 text-center">
                <small class="text-muted">
                    <i class="fas fa-mosque mr-1"></i>
                    Sistem Rasmi Jabatan Mufti Brunei
                </small>
            </p>
        </div>
    </div>
</div>
@endsection