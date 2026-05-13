@extends('layouts.admin.app')

@section('title', 'Data Siswa - ' . $classroom->name)

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Data Siswa - {{ $classroom->name }}</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}">Kelas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $classroom->name }}</li>
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
                        <h3 class="card-title">
                            <i class="bi bi-people-fill me-2"></i> Daftar Siswa Kelas {{ $classroom->name }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalStudent" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Siswa
                            </button>
                            <a href="{{ route('classrooms.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
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
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                <h5 class="modal-title" id="modalStudentLabel">Form Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="studentForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="student_id">
                <input type="hidden" name="classroom_id" id="classroom_id" value="{{ $classroom->id }}">
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
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Siswa akan ditambahkan ke kelas: <strong>{{ $classroom->name }}</strong>
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
                <p>Apakah Anda yakin ingin menghapus data siswa <strong id="student_name"></strong>?</p>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const classroomId = {{ $classroom->id }};
    
    const table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('classrooms.students.data', $classroom->id) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nis', name: 'nis' },
            { data: 'name', name: 'name' },
            { data: 'classroom_name', name: 'classroom.name' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            processing: "<div class='spinner-border text-primary' role='status'></div>",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
        },
        buttons: [
            { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-danger btn-sm' }
        ]
    });

    $('#studentForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#student_id').val();
        let url = id ? "{{ url('admin/students') }}/" + id : "{{ route('students.store') }}";
        
        if (id) {
            if ($('#studentForm input[name="_method"]').length === 0) {
                $('#studentForm').append('<input type="hidden" name="_method" value="PUT">');
            }
        } else {
            $('#studentForm input[name="_method"]').remove();
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#modalStudent').modal('hide');
                    resetForm();
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000 });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $.each(errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid').siblings('.invalid-feedback').text(value[0]);
                    });
                }
            }
        });
    });
});

function editStudent(id) {
    $.ajax({
        url: "{{ url('admin/students') }}/" + id + "/edit",
        type: "GET",
        success: function(data) {
            $('#student_id').val(data.id);
            $('#nis').val(data.nis);
            $('#name').val(data.name);
            $('#modalStudentLabel').text('Edit Data Siswa');
            $('#modalStudent').modal('show');
        }
    });
}

function confirmDelete(id, name) {
    $('#hapus_id').val(id);
    $('#student_name').text(name);
    $('#modalHapus').modal('show');
}

function deleteStudent() {
    const id = $('#hapus_id').val();
    $.ajax({
        url: "{{ url('admin/students') }}/" + id,
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#studentsTable').DataTable().ajax.reload();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000 });
            }
        }
    });
}

function resetForm() {
    $('#studentForm')[0].reset();
    $('#student_id').val('');
    $('#modalStudentLabel').text('Tambah Data Siswa');
    $('.is-invalid').removeClass('is-invalid');
    $('#studentForm input[name="_method"]').remove();
}
</script>
@endpush