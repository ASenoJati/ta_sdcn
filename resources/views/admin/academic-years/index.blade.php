@extends('layouts.admin.app')

@section('title', 'Manajemen Tahun Ajaran')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Tahun Ajaran</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Tahun Ajaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Tahun Ajaran</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAcademicYear" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Tahun Ajaran
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="academicYearsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Tahun Ajaran</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
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

<!-- Modal Form -->
<div class="modal fade" id="modalAcademicYear" tabindex="-1" aria-labelledby="modalAcademicYearLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAcademicYearLabel">Form Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="academicYearForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="academicYearId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: 2025/2026" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Tanggal selesai harus setelah tanggal mulai.</small>
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
                <p>Apakah Anda yakin ingin menghapus tahun ajaran <strong id="academicYearName"></strong>?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Perhatian: Tahun ajaran yang masih digunakan oleh data lain tidak dapat dihapus!</p>
                <input type="hidden" id="hapusId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteAcademicYear()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- 1. JQUERY HARUS PERTAMA -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- 2. DATATABLES -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- 3. SWEETALERT2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 4. SCRIPT KUSTOM -->
<script>
// Pastikan semua fungsi global didefinisikan di window
window.resetForm = function() {
    document.getElementById('academicYearForm').reset();
    document.getElementById('academicYearId').value = '';
    document.getElementById('modalAcademicYearLabel').textContent = 'Tambah Tahun Ajaran';
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    // Hapus _method jika ada
    const methodInput = document.querySelector('#academicYearForm input[name="_method"]');
    if (methodInput) methodInput.remove();
};

window.editAcademicYear = function(id) {
    fetch('{{ url("admin/academic-years") }}/' + id + '/edit')
        .then(response => response.json())
        .then(data => {
            document.getElementById('academicYearId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('start_date').value = data.start_date;
            document.getElementById('end_date').value = data.end_date;
            document.getElementById('modalAcademicYearLabel').textContent = 'Edit Tahun Ajaran';
            const modal = new bootstrap.Modal(document.getElementById('modalAcademicYear'));
            modal.show();
        })
        .catch(error => console.error('Error:', error));
};

window.confirmDelete = function(id, name) {
    document.getElementById('hapusId').value = id;
    document.getElementById('academicYearName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('modalHapus'));
    modal.show();
};

window.deleteAcademicYear = function() {
    const id = document.getElementById('hapusId').value;
    Swal.fire({
        title: 'Menghapus...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('{{ url("admin/academic-years") }}/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalHapus'));
            if (modal) modal.hide();
            $('#academicYearsTable').DataTable().ajax.reload();
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
        }
    })
    .catch(error => {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan. Silakan coba lagi.' });
    });
};

window.setActive = function(id) {
    Swal.fire({
        title: 'Aktifkan Tahun Ajaran?',
        text: "Data lain akan reset ke tahun ajaran ini.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, aktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mengaktifkan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('{{ url("admin/academic-years") }}/' + id + '/set-active', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#academicYearsTable').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.' });
            });
        }
    });
};

// ========== DOKUMEN READY ==========
$(document).ready(function() {
    // Inisialisasi DataTable
    const table = $('#academicYearsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('academic-years.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'period', name: 'period' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            processing: "<div class='spinner-border text-primary' role='status'></div>",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
        }
    });

    // Form submit
    $('#academicYearForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#academicYearId').val();
        let url = id ? '{{ url("admin/academic-years") }}/' + id : '{{ route("academic-years.store") }}';
        const method = id ? 'PUT' : 'POST';

        if (id && !$('#academicYearForm input[name="_method"]').length) {
            $(this).append('<input type="hidden" name="_method" value="PUT">');
        }

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#modalAcademicYear').modal('hide');
                    resetForm();
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                Swal.close();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $.each(errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid').siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                }
            }
        });
    });
});
</script>
@endpush