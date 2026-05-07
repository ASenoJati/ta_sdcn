@extends('layouts.admin.app')

@section('title', 'Manajemen User')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen User</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User</li>
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
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Data User</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah User
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="userTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status Verifikasi</th>
                                        <th>Tanggal Dibuat</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi oleh DataTables via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::App Content-->

<!-- Modal Form User -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserLabel">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger" id="password_required">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password (untuk edit).</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Pilih Role</option>
                                <!-- Data role akan diisi via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus user <strong id="user_name"></strong>?</p>
                <p class="text-danger"><small>Perhatian: Tindakan ini tidak dapat dibatalkan!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Document ready, initializing DataTable...');
    
    // Load data roles untuk dropdown
    loadRoles();
    
    // Inisialisasi DataTable
    const table = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('user.data') }}",
            type: "GET",
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data. Silakan refresh halaman.',
                    timer: 3000
                });
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role_name', name: 'role_name' },
            { data: 'email_verified', name: 'email_verified' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
            loadingRecords: "Memuat...",
            zeroRecords: "Tidak ada data ditemukan",
            emptyTable: "Tidak ada data",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
             "<'row'<'col-sm-12'B>>",
        buttons: [
            { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-info btn-sm' }
        ],
        responsive: true,
        order: [[1, 'asc']]
    });

    // Submit form via AJAX
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        const id = $('#user_id').val();
        let url, method;
        
        if (id) {
            // Untuk update
            url = "{{ url('admin/user') }}/" + id;
            method = 'POST';
            // Tambahkan _method=PUT untuk Laravel
            if ($('#userForm input[name="_method"]').length === 0) {
                $('#userForm').append('<input type="hidden" name="_method" value="PUT">');
            } else {
                $('#userForm input[name="_method"]').val('PUT');
            }
        } else {
            // Untuk store
            url = "{{ route('user.store') }}";
            method = 'POST';
            // Hapus _method jika ada
            $('#userForm input[name="_method"]').remove();
        }
        
        console.log('Submitting to:', url, 'Method:', method, 'ID:', id);
        
        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Success:', response);
                if (response.success) {
                    $('#modalUser').modal('hide');
                    resetForm();
                    table.ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    
                    $.each(errors, function(key, value) {
                        const input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(value[0]);
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan periksa kembali form Anda.',
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.',
                        timer: 3000
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

// Function to load roles for dropdown
function loadRoles() {
    $.ajax({
        url: "{{ route('user.roles') }}",
        type: "GET",
        success: function(data) {
            const select = $('#role_id');
            select.empty();
            select.append('<option value="">Pilih Role</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name.toUpperCase() + '</option>');
            });
        },
        error: function(xhr) {
            console.error('Error loading roles:', xhr);
        }
    });
}

// Function to edit user
function editUser(id) {
    console.log('Editing user ID:', id);
    
    const url = "{{ url('admin/user') }}/" + id + "/edit";
    
    $.ajax({
        url: url,
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('Edit success:', data);
            
            $('#user_id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role_id').val(data.role_id);
            
            // Password field tidak wajib saat edit
            $('#password').removeAttr('required');
            $('#password_required').text('(Opsional)');
            
            $('#modalUserLabel').text('Edit Data User');
            $('#modalUser').modal('show');
        },
        error: function(xhr) {
            console.error('Edit error:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data tidak ditemukan.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Function to confirm delete
function confirmDelete(id, name) {
    $('#hapus_id').val(id);
    $('#user_name').text(name);
    $('#modalHapus').modal('show');
}

// Function to delete user
function deleteUser() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/user') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#userTable').DataTable().ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: xhr.responseJSON?.message || 'Terjadi kesalahan. Silakan coba lagi.',
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
}

// Reset form
function resetForm() {
    $('#userForm')[0].reset();
    $('#user_id').val('');
    $('#modalUserLabel').text('Tambah Data User');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#userForm input[name="_method"]').remove();
    
    // Password field wajib saat tambah
    $('#password').attr('required', 'required');
    $('#password_required').text('*');
}
</script>
@endpush