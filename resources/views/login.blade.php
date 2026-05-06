@extends('layouts.public.app')

@section('title', 'SD Cahaya Nur | Login Page')

@section('body_content')
<body class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="login-logo">
        <a href="{{ url('/') }}">SD Cahaya Nur</a>
      </div>
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Sign in to start your session</p>

          {{-- Alert Error --}}
          @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
          @endif

          <form action="{{ url('/login') }}" method="post">
            @csrf
            <div class="input-group mb-3">
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required />
              <div class="input-group-text">
                <span class="bi bi-envelope"></span>
              </div>
            </div>
            
            <div class="input-group mb-3">
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>
            
            <div class="row">
              <div class="col-8">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                      Ingat Saya
                    </label>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Sign In</button>
                </div>
              </div>
            </div>
          </form>

          <div class="social-auth-links text-center mb-3 d-grid gap-2">
            <p>- OR -</p>
            <a href="#" class="btn btn-danger">
              <i class="bi bi-google me-2"></i> Sign in using Google+
            </a>
          </div>

          <p class="mb-1">
            <a href="{{ url('/forgot-password') }}">Lupa password</a>
          </p>
        </div>
      </div>
    </div>
</body>
@endsection