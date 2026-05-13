@extends('layouts.admin.app')

@section('title', 'Profil Saya')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Profil Saya</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profil</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Image Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ $user->avatar ? asset($user->avatar) : asset('assets/img/logo-school.png') }}"
                                 alt="User profile picture"
                                 id="profileImage"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>

                        <h3 class="profile-username text-center">{{ $user->name }}</h3>

                        <p class="text-muted text-center">
                            {{ ucfirst($user->roles->first()->name ?? 'User') }}
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-end">{{ $user->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Member Since</b> <a class="float-end">{{ $user->created_at->format('d F Y') }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="float-end">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            
            <div class="col-md-8">
                <!-- Profile Update Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-person-badge me-2"></i> Edit Profil
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ $user->name }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="email" class="form-label">Alamat Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ $user->email }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="avatar" class="form-label">Foto Profil</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar" 
                                           accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" onclick="updateProfile()">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
                
                <!-- Change Password Card -->
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-key-fill me-2"></i> Ganti Password
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="current_password" 
                                           name="current_password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" 
                                           name="new_password" required>
                                    <div class="invalid-feedback"></div>
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger" onclick="updatePassword()">
                            <i class="bi bi-shield-lock-fill me-1"></i> Ganti Password
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
</div>
<!--end::App Content-->
@endsection

@push('styles')
<style>
    .profile-user-img {
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
        width: 150px;
        height: 150px;
        object-fit: cover;
    }
    
    .list-group-item {
        padding: 12px 15px;
    }
    
    .card {
        margin-bottom: 20px;
    }
    
    .btn {
        border-radius: 5px;
        padding: 8px 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Update profile
    function updateProfile() {
        const formData = new FormData(document.getElementById('profileForm'));
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("user.update-profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan. Silakan coba lagi.'
            });
        });
    }
    
    // Update password
    function updatePassword() {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Validasi sederhana
        if (!currentPassword || !newPassword || !confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Semua field password harus diisi!'
            });
            return;
        }
        
        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Password baru dan konfirmasi password tidak sama!'
            });
            return;
        }
        
        if (newPassword.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Password baru minimal 6 karakter!'
            });
            return;
        }
        
        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);
        
        // Show loading
        Swal.fire({
            title: 'Mengganti Password...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("user.update-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Clear password fields
                    document.getElementById('current_password').value = '';
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                });
            } else if (data.errors) {
                let errorMsg = '';
                for (let key in data.errors) {
                    errorMsg += data.errors[key][0] + '\n';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: errorMsg
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan. Silakan coba lagi.'
            });
        });
    }
    
    // Preview avatar before upload
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profileImage').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush