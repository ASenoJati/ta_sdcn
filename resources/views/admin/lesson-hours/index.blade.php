@extends('layouts.admin.app')

@section('title', 'Manajemen Jam Pembelajaran')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Jam Pembelajaran</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jam Pembelajaran</li>
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
                        <h3 class="card-title">Data Jam Pembelajaran</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalLessonHour" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Jam Pembelajaran
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="lessonHourTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Sesi</th>
                                        <th>Waktu Mulai - Selesai</th>
                                        <th>Durasi</th>
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

<!-- Modal Form Lesson Hour -->
<div class="modal fade" id="modalLessonHour" tabindex="-1" aria-labelledby="modalLessonHourLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLessonHourLabel">Form Jam Pembelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="lessonHourForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="lesson_hour_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="session" class="form-label">Sesi / Jam ke- <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="session" name="session" min="1" max="20" placeholder="Contoh: 1, 2, 3, dst" required>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Nomor urut jam pembelajaran (harus unik)</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Harus setelah waktu mulai</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Setiap sesi/jam harus memiliki nomor yang unik</li>
                            <li>Kombinasi waktu mulai dan selesai harus unik (tidak boleh sama dengan data lain)</li>
                            <li>Waktu selesai harus lebih besar dari waktu mulai</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning" id="durationPreview" style="display: none;">
                        <i class="bi bi-clock"></i>
                        <strong>Durasi:</strong> <span id="previewDuration">-</span>
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
                <p>Apakah Anda yakin ingin menghapus <strong id="lesson_hour_name"></strong>?</p>
                <p class="text-warning"><small>Perhatian: Tindakan ini tidak dapat dibatalkan!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteLessonHour()">Hapus</button>
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
    
    // Inisialisasi DataTable
    const table = $('#lessonHourTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('lesson-hours.data') }}",
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
            { data: 'session_formatted', name: 'session' },
            { data: 'time_range', name: 'time_range' },
            { data: 'duration', name: 'duration', orderable: false },
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
    
    // Preview duration when time changes
    function previewDuration() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        if (startTime && endTime) {
            const start = new Date('1970-01-01T' + startTime + ':00');
            const end = new Date('1970-01-01T' + endTime + ':00');
            
            if (end > start) {
                const diffMs = end - start;
                const diffMinutes = diffMs / (1000 * 60);
                
                let durationText = '';
                if (diffMinutes >= 60) {
                    const hours = Math.floor(diffMinutes / 60);
                    const minutes = diffMinutes % 60;
                    if (minutes > 0) {
                        durationText = hours + ' jam ' + minutes + ' menit';
                    } else {
                        durationText = hours + ' jam';
                    }
                } else {
                    durationText = diffMinutes + ' menit';
                }
                
                $('#previewDuration').text(durationText);
                $('#durationPreview').show();
            } else {
                $('#durationPreview').hide();
            }
        } else {
            $('#durationPreview').hide();
        }
    }
    
    $('#start_time, #end_time').on('change', previewDuration);
    
    // Submit form via AJAX
    $('#lessonHourForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate time
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        if (startTime >= endTime) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Waktu Selesai harus lebih besar dari Waktu Mulai!',
                timer: 3000
            });
            return;
        }
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        const id = $('#lesson_hour_id').val();
        let url, method;
        
        if (id) {
            // Untuk update
            url = "{{ url('admin/lesson-hours') }}/" + id;
            method = 'POST';
            // Tambahkan _method=PUT untuk Laravel
            if ($('#lessonHourForm input[name="_method"]').length === 0) {
                $('#lessonHourForm').append('<input type="hidden" name="_method" value="PUT">');
            } else {
                $('#lessonHourForm input[name="_method"]').val('PUT');
            }
        } else {
            // Untuk store
            url = "{{ route('lesson-hours.store') }}";
            method = 'POST';
            // Hapus _method jika ada
            $('#lessonHourForm input[name="_method"]').remove();
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
                    $('#modalLessonHour').modal('hide');
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

// Function to edit lesson hour
function editLessonHour(id) {
    console.log('Editing lesson hour ID:', id);
    
    const url = "{{ url('admin/lesson-hours') }}/" + id + "/edit";
    
    $.ajax({
        url: url,
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('Edit success:', data);
            
            $('#lesson_hour_id').val(data.id);
            $('#session').val(data.session);
            $('#start_time').val(data.start_time);
            $('#end_time').val(data.end_time);
            
            // Trigger preview
            $('#start_time, #end_time').trigger('change');
            
            $('#modalLessonHourLabel').text('Edit Jam Pembelajaran');
            $('#modalLessonHour').modal('show');
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
    $('#lesson_hour_name').text(name);
    $('#modalHapus').modal('show');
}

// Function to delete lesson hour
function deleteLessonHour() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/lesson-hours') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#lessonHourTable').DataTable().ajax.reload();
                
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
    $('#lessonHourForm')[0].reset();
    $('#lesson_hour_id').val('');
    $('#modalLessonHourLabel').text('Tambah Jam Pembelajaran');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#lessonHourForm input[name="_method"]').remove();
    $('#durationPreview').hide();
}
</script>
@endpush