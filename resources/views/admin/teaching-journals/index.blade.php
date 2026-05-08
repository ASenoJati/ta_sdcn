@extends('layouts.admin.app')

@section('title', 'Manajemen Jurnal Pembelajaran')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Jurnal Pembelajaran</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jurnal Pembelajaran</li>
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
                        <h3 class="card-title">Data Jurnal Pembelajaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="teachingJournalTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Jadwal</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="25%">Materi</th>
                                        <th width="15%">Presensi</th>
                                        <th width="10%">Dibuat</th>
                                        <th width="15%">Aksi</th>
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

<!-- Modal Detail Jurnal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="bi bi-journal-bookmark-fill"></i> Detail Jurnal Pembelajaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
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
                <p>Apakah Anda yakin ingin menghapus jurnal pembelajaran:</p>
                <p class="fw-bold text-center" id="journal_info"></p>
                <p class="text-warning"><small>Perhatian: Menghapus jurnal juga akan menghapus semua data presensi siswa!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteJournal()">Hapus</button>
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
    .attendance-table {
        font-size: 14px;
    }
    .attendance-table th {
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 12px;
        padding: 5px 10px;
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
    
    // Inisialisasi DataTable
    const table = $('#teachingJournalTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('teaching-journals.data') }}",
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
            { data: 'schedule_info', name: 'schedule_info' },
            { data: 'date_info', name: 'date' },
            { data: 'material_preview', name: 'material' },
            { data: 'attendance_summary', name: 'attendance_summary', orderable: false },
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
        order: [[5, 'desc']]
    });
});

// Function to view detail
function viewDetail(id) {
    $('#modalDetail').modal('show');
    
    $.ajax({
        url: "{{ url('admin/teaching-journals') }}/" + id,
        type: "GET",
        success: function(data) {
            console.log('Detail data:', data);
            displayDetail(data);
        },
        error: function(xhr) {
            console.error('Error loading detail:', xhr);
            $('#detailContent').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Gagal memuat data. Silakan coba lagi.
                </div>
            `);
        }
    });
}

// Function to display detail
function displayDetail(data) {
    const schedule = data.teaching_schedule;
    const teacher = schedule.teacher || { name: '-' };
    const attendances = data.student_attendances || [];
    const summary = data.attendance_summary;
    
    let attendanceHtml = '';
    if (attendances.length > 0) {
        attendanceHtml = `
            <div class="table-responsive">
                <table class="table table-bordered table-striped attendance-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${attendances.map((item, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.student.nis}</td>
                                <td>${item.student.name}</td>
                                <td>${item.status_badge}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    } else {
        attendanceHtml = '<div class="alert alert-warning">Belum ada data presensi siswa.</div>';
    }
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-info-circle"></i> Informasi Jadwal
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="35%">Mata Pelajaran</th>
                                <td>: <strong>${schedule.subject.name}</strong></td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td>: <strong>${schedule.classroom.name}</strong></td>
                            </tr>
                            <tr>
                                <th>Guru</th>
                                <td>: ${teacher.name}</td>
                            </tr>
                            <tr>
                                <th>Hari, Tanggal</th>
                                <td>: <strong>${data.day_name}, ${data.date_formatted}</strong></td>
                            </tr>
                            <tr>
                                <th>Jam Pelajaran</th>
                                <td>: Jam ke-${schedule.lesson_hour.session} (${schedule.lesson_hour.start_time} - ${schedule.lesson_hour.end_time})</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-graph-up"></i> Ringkasan Presensi
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="alert alert-success mb-0">
                                    <h4>${summary.hadir}</h4>
                                    <small>Hadir</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="alert alert-warning mb-0">
                                    <h4>${summary.izin}</h4>
                                    <small>Izin</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="alert alert-info mb-0">
                                    <h4>${summary.sakit}</h4>
                                    <small>Sakit</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="alert alert-danger mb-0">
                                    <h4>${summary.alpa}</h4>
                                    <small>Alpa</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Total Siswa: ${summary.total}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-journal-bookmark-fill"></i> Materi Pembelajaran
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${data.material}</p>
                    </div>
                </div>
            </div>
        </div>
        ${data.reflection ? `
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-chat-quote-fill"></i> Refleksi
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${data.reflection}</p>
                    </div>
                </div>
            </div>
        </div>
        ` : ''}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-people-fill"></i> Daftar Presensi Siswa
                    </div>
                    <div class="card-body">
                        ${attendanceHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#detailContent').html(html);
}

// Function to confirm delete
function confirmDelete(id, info) {
    $('#hapus_id').val(id);
    $('#journal_info').html(info);
    $('#modalHapus').modal('show');
}

// Function to delete journal
function deleteJournal() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/teaching-journals') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#teachingJournalTable').DataTable().ajax.reload();
                
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
</script>
@endpush