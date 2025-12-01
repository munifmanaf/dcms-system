

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Masuk - Sistem Koleksi Digital</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <!-- Custom styles -->
    <style>
        .login-logo {
            text-align: center;
            margin-bottom: 1rem;
        }
        .login-logo i {
            color: #1a472a;
            font-size: 3rem;
        }
        .login-logo h2 {
            color: #1a472a;
            font-family: 'Amiri', serif;
            font-weight: bold;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .login-logo p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        .login-card-body {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #1a472a 0%, #2c5530 100%);
            border: none;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2c5530 0%, #1a472a 100%);
            color: white;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <i class="fas fa-mosque"></i>
        <h2>Sistem Koleksi Digital</h2>
        <p>Jabatan Mufti Brunei</p>
    </div>
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('status') }}
        </div>
    @endif

    <!-- /.login-logo -->
    <div class="card login-card-body">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Log masuk untuk mengakses sistem</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Kata Laluan" required autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-login btn-block">Log Masuk</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            @if (Route::has('password.request'))
                <p class="mb-1 mt-3">
                    <a href="{{ route('password.request') }}">Lupa kata laluan?</a>
                </p>
            @endif
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>