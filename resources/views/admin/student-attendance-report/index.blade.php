@extends('layouts.admin.app')

@section('title', 'Rekap Presensi per Kelas')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Rekap Presensi per Kelas</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Rekap Presensi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <!-- Filter Form -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-funnel me-2"></i> Filter Laporan
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('attendance-report.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="classroom_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select class="form-select" id="classroom_id" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hasil Laporan -->
        @if($classroomId && $startDate && $endDate)
            <div class="card card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-table me-2"></i>
                        Rekap Presensi {{ $selectedClassroom ? $selectedClassroom->name : '' }}
                     <small class="ms-2 text-white">
    ({{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }})
</small>
                    </h3>
                    <div class="card-tools">
                        <span class="badge bg-info">Total Siswa: {{ count($reportData) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($reportData) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center bg-warning text-dark">Izin</th>
                                        <th class="text-center bg-info text-white">Sakit</th>
                                        <th class="text-center bg-danger text-white">Alpa</th>
                                        <th class="text-center bg-secondary text-white">Total</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row['nis'] }}</td>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-center">{{ $row['izin'] }}</td>
                                            <td class="text-center">{{ $row['sakit'] }}</td>
                                            <td class="text-center">{{ $row['alpa'] }}</td>
                                            <td class="text-center"><strong>{{ $row['total_tidak_hadir'] }}</strong></td>
                                            <td class="text-center">
                                                <button type="button" 
                                                        class="btn btn-info btn-sm btn-detail" 
                                                        data-student-id="{{ $row['student_id'] }}"
                                                        data-student-name="{{ $row['name'] }}">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="3" class="text-end">Total Keseluruhan:</td>
                                        <td class="text-center">{{ array_sum(array_column($reportData, 'izin')) }}</td>
                                        <td class="text-center">{{ array_sum(array_column($reportData, 'sakit')) }}</td>
                                        <td class="text-center">{{ array_sum(array_column($reportData, 'alpa')) }}</td>
                                        <td class="text-center">{{ array_sum(array_column($reportData, 'total_tidak_hadir')) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            Tidak ada data presensi untuk kelas ini pada rentang tanggal yang dipilih.
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                </div>
            </div>
        @else
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">Silakan pilih kelas dan rentang tanggal</h4>
                    <p class="text-muted">Pilih kelas, tanggal mulai dan selesai, lalu klik Tampilkan.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-person me-2"></i> Detail Presensi Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nama Siswa:</strong> <span id="modalStudentName">-</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Tidak Hadir:</strong> <span id="modalTotalNotPresent" class="badge bg-danger">0</span>
                    </div>
                </div>
                <hr>
                <div id="modalContent">
                    <div class="text-center py-4">
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .table th {
        vertical-align: middle;
        text-align: center;
    }
    .table td {
        vertical-align: middle;
    }
    tfoot td {
        background-color: #f8f9fa;
    }
    .btn-detail {
        padding: 4px 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        const table = $('#reportTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                zeroRecords: "Tidak ada data ditemukan",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            pageLength: 25,
            order: [[2, 'asc']]
        });

        // Event listener untuk tombol detail
        $('.btn-detail').on('click', function() {
            const studentId = $(this).data('student-id');
            const studentName = $(this).data('student-name');
            const classroomId = $('#classroom_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // Set judul modal
            $('#modalStudentName').text(studentName);
            $('#modalTotalNotPresent').text('...');
            $('#modalContent').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);

            $('#detailModal').modal('show');

            // Fetch data detail
            $.ajax({
                url: "{{ route('attendance-report.detail') }}",
                type: "GET",
                data: {
                    student_id: studentId,
                    classroom_id: classroomId,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalTotalNotPresent').text(response.total);

                        let html = '';
                        if (response.data.length > 0) {
                            html = `
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Hari</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Materi</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            response.data.forEach((item, index) => {
                                html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.date}</td>
                                        <td>${item.day}</td>
                                        <td>${item.subject}</td>
                                        <td>${item.material}</td>
                                        <td>${item.status_badge}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        } else {
                            html = `
                                <div class="alert alert-success text-center">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Siswa ini memiliki presensi lengkap (tidak ada izin, sakit, atau alpa)</strong>
                                    pada rentang tanggal yang dipilih.
                                </div>
                            `;
                        }

                        $('#modalContent').html(html);
                    } else {
                        $('#modalContent').html(`
                            <div class="alert alert-danger text-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                ${response.message || 'Gagal memuat data.'}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#modalContent').html(`
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Terjadi kesalahan. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        });
    });
</script>
@endpush