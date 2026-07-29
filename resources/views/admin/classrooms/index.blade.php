@extends('layouts.admin.app')

@section('title', 'Manajemen Kelas')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Kelas</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelas</li>
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
                        <h3 class="card-title">Data Kelas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalClassroom" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Kelas
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="classroomTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Kelas</th>
                                        {{-- <th>Deskripsi</th> --}}
                                        <th>Jumlah Siswa</th>
                                        <th>Tanggal Dibuat</th>
                                        <th width="20%">Aksi</th>
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

<!-- Modal Form Classroom -->
<div class="modal fade" id="modalClassroom" tabindex="-1" aria-labelledby="modalClassroomLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClassroomLabel">Form Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="classroomForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="classroom_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: X RPL 1" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                <p>Apakah Anda yakin ingin menghapus kelas <strong id="classroom_name"></strong>?</p>
                <p class="text-danger"><small>Perhatian: Kelas yang memiliki siswa tidak dapat dihapus!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteClassroom()">Hapus</button>
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
    const table = $('#classroomTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('classrooms.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            // { data: 'description_short', name: 'description' },
            { data: 'students_count', name: 'students_count' },
            { data: 'created_at_formatted', name: 'created_at' },
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

    $('#classroomForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#classroom_id').val();
        let url = id ? "{{ url('admin/classrooms') }}/" + id : "{{ route('classrooms.store') }}";
        let method = id ? 'POST' : 'POST';
        
        if (id) {
            if ($('#classroomForm input[name="_method"]').length === 0) {
                $('#classroomForm').append('<input type="hidden" name="_method" value="PUT">');
            }
        } else {
            $('#classroomForm input[name="_method"]').remove();
        }
        
        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#modalClassroom').modal('hide');
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

function editClassroom(id) {
    $.ajax({
        url: "{{ url('admin/classrooms') }}/" + id + "/edit",
        type: "GET",
        success: function(data) {
            $('#classroom_id').val(data.id);
            $('#name').val(data.name);
            $('#description').val(data.description);
            $('#modalClassroomLabel').text('Edit Data Kelas');
            $('#modalClassroom').modal('show');
        }
    });
}

function confirmDelete(id, name) {
    $('#hapus_id').val(id);
    $('#classroom_name').text(name);
    $('#modalHapus').modal('show');
}

function deleteClassroom() {
    const id = $('#hapus_id').val();
    $.ajax({
        url: "{{ url('admin/classrooms') }}/" + id,
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#classroomTable').DataTable().ajax.reload();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000 });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: response.message });
            }
        }
    });
}

function resetForm() {
    $('#classroomForm')[0].reset();
    $('#classroom_id').val('');
    $('#modalClassroomLabel').text('Tambah Data Kelas');
    $('.is-invalid').removeClass('is-invalid');
    $('#classroomForm input[name="_method"]').remove();
}
</script>
@endpush