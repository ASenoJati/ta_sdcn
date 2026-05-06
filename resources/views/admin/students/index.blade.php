@extends('layouts.admin.app')

@section('title', 'Manajemen Students')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Students</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
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
                        <h3 class="card-title">Data Students</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalStudent" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Student
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="studentsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>Kelas</th>
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

<!-- Modal Form Student -->
<div class="modal fade" id="modalStudent" tabindex="-1" aria-labelledby="modalStudentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalStudentLabel">Form Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="studentForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="student_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nis" name="nis" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="classroom_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select class="form-select" id="classroom_id" name="classroom_id" required>
                            <option value="">Pilih Kelas</option>
                            <!-- Data kelas akan diisi via AJAX -->
                        </select>
                        <div class="invalid-feedback"></div>
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
                <p>Apakah Anda yakin ingin menghapus data student <strong id="student_name"></strong>?</p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteStudent()">Hapus</button>
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
    
    // Load data kelas untuk dropdown
    loadClassrooms();
    
    // Inisialisasi DataTable
    const table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('students.data') }}",
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
            { data: 'nis', name: 'nis' },
            { data: 'name', name: 'name' },
            { data: 'classroom', name: 'classroom.name' },
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
    $('#studentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        const id = $('#student_id').val();
        let url, method;
        
        if (id) {
            // Untuk update - gunakan route dengan parameter
            url = "{{ url('admin/students') }}/" + id;
            method = 'POST';
            // Tambahkan _method=PUT untuk Laravel
            if ($('#studentForm input[name="_method"]').length === 0) {
                $('#studentForm').append('<input type="hidden" name="_method" value="PUT">');
            } else {
                $('#studentForm input[name="_method"]').val('PUT');
            }
        } else {
            // Untuk store
            url = "{{ route('students.store') }}";
            method = 'POST';
            // Hapus _method jika ada
            $('#studentForm input[name="_method"]').remove();
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
                    $('#modalStudent').modal('hide');
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

// Function to load classrooms for dropdown
function loadClassrooms() {
    $.ajax({
        url: "{{ route('classrooms.list') }}",
        type: "GET",
        success: function(data) {
            const select = $('#classroom_id');
            select.empty();
            select.append('<option value="">Pilih Kelas</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        },
        error: function(xhr) {
            console.error('Error loading classrooms:', xhr);
        }
    });
}

// Function to edit student
function editStudent(id) {
    console.log('Editing student ID:', id);
    
    // Gunakan route resource yang sudah ada
    const url = "{{ url('admin/students') }}/" + id + "/edit";
    
    $.ajax({
        url: url,
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('Edit success:', data);
            
            $('#student_id').val(data.id);
            $('#nis').val(data.nis);
            $('#name').val(data.name);
            $('#classroom_id').val(data.classroom_id);
            
            $('#modalStudentLabel').text('Edit Data Student');
            $('#modalStudent').modal('show');
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
    $('#student_name').text(name);
    $('#modalHapus').modal('show');
}

// Function to delete student
function deleteStudent() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/students') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#studentsTable').DataTable().ajax.reload();
                
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
    $('#studentForm')[0].reset();
    $('#student_id').val('');
    $('#modalStudentLabel').text('Tambah Data Student');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#studentForm input[name="_method"]').remove();
}

// Format NIS hanya angka
$('#nis').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush