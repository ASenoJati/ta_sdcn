@extends('layouts.admin.app')

@section('title', 'Manajemen Waktu Presensi Role')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Waktu Presensi Role</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Waktu Presensi Role</li>
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
                        <h3 class="card-title">Data Waktu Presensi Role</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRoleAttendanceTime" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Waktu Presensi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="roleAttendanceTimeTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Role</th>
                                        <th>Setting Waktu</th>
                                        <th>Waktu Check-in</th>
                                        <th>Waktu Check-out</th>
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

<!-- Modal Form Role Attendance Time -->
<div class="modal fade" id="modalRoleAttendanceTime" tabindex="-1" aria-labelledby="modalRoleAttendanceTimeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRoleAttendanceTimeLabel">Form Waktu Presensi Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleAttendanceTimeForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="role_attendance_time_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Pilih Role</option>
                            <!-- Data role akan diisi via AJAX -->
                        </select>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">⚠️ Setiap role hanya dapat memiliki SATU pengaturan waktu presensi</small>
                    </div>
                    <div class="mb-3">
                        <label for="attendance_time_settings_id" class="form-label">Setting Waktu Presensi <span class="text-danger">*</span></label>
                        <select class="form-select" id="attendance_time_settings_id" name="attendance_time_settings_id" required>
                            <option value="">Pilih Setting Waktu</option>
                            <!-- Data setting akan diisi via AJAX -->
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-info mt-3" id="previewTimeInfo" style="display: none;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Detail Setting Waktu:</strong><br>
                        <span id="previewCheckIn">-</span><br>
                        <span id="previewCheckOut">-</span><br>
                        <span id="previewGracePeriod">-</span>
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
                <p>Apakah Anda yakin ingin menghapus waktu presensi untuk role <strong id="role_name"></strong>?</p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteRoleAttendanceTime()">Hapus</button>
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
<style>
    .select2-container .select2-selection--single {
        height: 38px;
    }
</style>
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
    loadAttendanceSettings();
    
    // Inisialisasi DataTable
    const table = $('#roleAttendanceTimeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('role-attendance-times.data') }}",
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
            { data: 'role_name', name: 'role_name' },
            { data: 'attendance_name', name: 'attendance_time_settings.name' },
            { data: 'check_in_time', name: 'check_in_time' },
            { data: 'check_out_time', name: 'check_out_time' },
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

    // Preview waktu ketika memilih setting
    $('#attendance_time_settings_id').on('change', function() {
        const selectedId = $(this).val();
        if (selectedId) {
            $.ajax({
                url: "{{ route('attendance-settings.list') }}",
                type: "GET",
                success: function(settings) {
                    const setting = settings.find(s => s.id == selectedId);
                    if (setting) {
                        $('#previewCheckIn').html('<strong>Check-in:</strong> ' + setting.check_in_start + ' - ' + setting.check_in_end);
                        $('#previewCheckOut').html('<strong>Check-out:</strong> ' + setting.check_out_start + ' - ' + setting.check_out_end);
                        $('#previewGracePeriod').html('<strong>Grace Period:</strong> ' + setting.grace_period_minutes + ' menit');
                        $('#previewTimeInfo').show();
                    }
                }
            });
        } else {
            $('#previewTimeInfo').hide();
        }
    });

    // Submit form via AJAX
    $('#roleAttendanceTimeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        const id = $('#role_attendance_time_id').val();
        let url, method;
        
        if (id) {
            // Untuk update
            url = "{{ url('admin/role-attendance-times') }}/" + id;
            method = 'POST';
            // Tambahkan _method=PUT untuk Laravel
            if ($('#roleAttendanceTimeForm input[name="_method"]').length === 0) {
                $('#roleAttendanceTimeForm').append('<input type="hidden" name="_method" value="PUT">');
            } else {
                $('#roleAttendanceTimeForm input[name="_method"]').val('PUT');
            }
        } else {
            // Untuk store
            url = "{{ route('role-attendance-times.store') }}";
            method = 'POST';
            // Hapus _method jika ada
            $('#roleAttendanceTimeForm input[name="_method"]').remove();
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
                    $('#modalRoleAttendanceTime').modal('hide');
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
                    
                    if (errors) {
                        $.each(errors, function(key, value) {
                            const input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(value[0]);
                        });
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: xhr.responseJSON.message || 'Silakan periksa kembali form Anda.',
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

// Function to load attendance settings for dropdown
function loadAttendanceSettings() {
    $.ajax({
        url: "{{ route('attendance-settings.list') }}",
        type: "GET",
        success: function(data) {
            const select = $('#attendance_time_settings_id');
            select.empty();
            select.append('<option value="">Pilih Setting Waktu</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + ' (Check-in: ' + value.check_in_start + ' - ' + value.check_in_end + ')</option>');
            });
        },
        error: function(xhr) {
            console.error('Error loading attendance settings:', xhr);
        }
    });
}

// Function to edit role attendance time
function editRoleAttendanceTime(id) {
    console.log('Editing role attendance time ID:', id);
    
    const url = "{{ url('admin/role-attendance-times') }}/" + id + "/edit";
    
    $.ajax({
        url: url,
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('Edit success:', data);
            
            $('#role_attendance_time_id').val(data.id);
            $('#role_id').val(data.role_id);
            $('#attendance_time_settings_id').val(data.attendance_time_settings_id);
            
            // Trigger preview
            $('#attendance_time_settings_id').trigger('change');
            
            $('#modalRoleAttendanceTimeLabel').text('Edit Waktu Presensi Role');
            $('#modalRoleAttendanceTime').modal('show');
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
function confirmDelete(id, roleName) {
    $('#hapus_id').val(id);
    $('#role_name').text(roleName.toUpperCase());
    $('#modalHapus').modal('show');
}

// Function to delete role attendance time
function deleteRoleAttendanceTime() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/role-attendance-times') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#roleAttendanceTimeTable').DataTable().ajax.reload();
                
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
    $('#roleAttendanceTimeForm')[0].reset();
    $('#role_attendance_time_id').val('');
    $('#modalRoleAttendanceTimeLabel').text('Tambah Waktu Presensi Role');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#roleAttendanceTimeForm input[name="_method"]').remove();
    $('#previewTimeInfo').hide();
}
</script>
@endpush