@extends('layouts.public.app')

@section('title', 'SD Cahaya Nur | Login Page')

@section('body_content')
<body class="login-page bg-body-secondary">
    <div class="login-box" style="animation: fadeInUp 0.5s ease;">
        <div class="login-logo">
            <div class="text-center mb-3">
                <img src="{{ asset('assets/img/logo-school.png') }}" alt="Logo SD Cahaya Nur" 
                     style="height: 70px; width: auto; margin-bottom: 10px;" 
                     onerror="this.src='https://via.placeholder.com/70?text=Logo'">
            </div>
            <a href="{{ url('/') }}" class="fw-bold" style="font-size: 1.8rem;">
                <span class="text-primary">SD</span> <span class="text-info">Cahaya Nur</span>
            </a>
        </div>
        
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body login-card-body p-4">
                <p class="login-box-msg">
                    <i class="bi bi-shield-lock-fill text-primary me-1"></i>
                    Selamat Datang! Silakan login untuk melanjutkan
                </p>

                <form action="{{ url('/login') }}" method="post" id="loginForm">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Email" value="{{ old('email') }}" required />
                        <div class="input-group-text">
                            <span class="bi bi-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <div class="text-danger mb-2 small">{{ $message }}</div>
                    @enderror
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Password" required />
                        <div class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    @error('password')
                        <div class="text-danger mb-2 small">{{ $message }}</div>
                    @enderror
                    
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
                                <button type="submit" class="btn btn-primary" id="btnLogin">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="social-auth-links text-center mb-3 d-grid gap-2 mt-4">
                    <p class="mb-2 text-muted">- ATAU -</p>
                    <a href="#" class="btn btn-danger" id="googleLoginBtn">
                        <i class="bi bi-google me-2"></i> Sign in using Google+
                    </a>
                </div>

                <p class="mb-0 text-center">
                    <a href="{{ url('/forgot-password') }}" class="text-muted">
                        <i class="bi bi-question-circle me-1"></i> Lupa password?
                    </a>
                </p>
            </div>
        </div>
    </div>

    <style>
        
        .login-box {
            animation: fadeInUp 0.5s ease;
        }
        
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }
        
        .btn-danger {
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .login-logo img {
            transition: transform 0.3s ease;
        }
        
        .login-logo img:hover {
            transform: scale(1.05);
        }
        
        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .input-group-text {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .input-group-text:hover {
            background-color: #e9ecef;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.input-group-text:last-child .bi');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-lock-fill');
                icon.classList.add('bi-unlock-fill');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-unlock-fill');
                icon.classList.add('bi-lock-fill');
            }
        }
        
        // Tampilkan error dari Laravel jika ada (untuk server-side validation)
        @if($errors->any())
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '<li>{{ $error }}</li>';
            @endforeach
            
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                html: '<ul class="text-start mb-0">' + errorMessages + '</ul>',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Coba Lagi',
                didOpen: () => {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.animation = 'none';
                        popup.offsetHeight;
                        popup.style.animation = null;
                    }
                }
            });
        @endif
        
        // Tampilkan pesan session success jika ada
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#007bff',
                timer: 3000,
                showConfirmButton: true
            });
        @endif
        
        // Tampilkan pesan error dari session (misal logout)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545',
                timer: 3000
            });
        @endif
        
        // Tampilkan pesan info dari session
        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '{{ session('info') }}',
                confirmButtonColor: '#17a2b8',
                timer: 3000
            });
        @endif
        
        // Loading effect on form submit
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btnLogin');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
                btnLogin.classList.add('btn-loading');
            });
        }
        
        // Google Login demo alert
        const googleBtn = document.getElementById('googleLoginBtn');
        if (googleBtn) {
            googleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Fitur Google Login',
                    text: 'Fitur ini akan segera tersedia!',
                    confirmButtonColor: '#007bff',
                    timer: 2000
                });
            });
        }
    </script>
</body>
@endsection