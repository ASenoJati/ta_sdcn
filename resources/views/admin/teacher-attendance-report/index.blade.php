@extends('layouts.admin.app')

@section('title', 'Rekap Presensi Guru')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0"><i class="bi bi-person-badge me-2"></i> Rekap Presensi Guru</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Rekap Presensi Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">

        <!-- Filter -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="card-title m-0">
                    <i class="bi bi-funnel me-2"></i> Filter Laporan
                </h5>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="filter_teacher" class="form-label">Guru</label>
                        <select id="filter_teacher" name="user_id" class="form-select">
                            <option value="">Semua Guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="#" id="exportExcel" class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grafik -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="bi bi-bar-chart me-2"></i> Grafik Presensi Harian
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="bi bi-table me-2"></i> Data Presensi Guru
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>Hadir</th>
                                        <th>Terlambat</th>
                                        <th>Total Presensi</th>
                                        <th>Persentase Kehadiran</th>
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .card-header {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let chartInstance = null;
    let table = null;

    function initTable() {
        table = $('#reportTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('teacher-attendance-report.data') }}",
                type: "GET",
                data: function(d) {
                    d.user_id = $('#filter_teacher').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                dataSrc: function(json) {
                    return json;
                }
            },
            columns: [
                { data: null, render: function(data, type, row, meta) { return meta.row + 1; } },
                { data: 'name' },
                { data: 'hadir' },
                { data: 'late' },
                { data: 'total' },
                { data: 'hadir_percentage', render: function(data) { return data + '%'; } }
            ],
            language: {
                processing: "<div class='spinner-border text-primary' role='status'></div>",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            order: [[1, 'asc']]
        });
    }

    function loadChart() {
        const params = {
            user_id: $('#filter_teacher').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        };

        $.ajax({
            url: "{{ route('teacher-attendance-report.chart') }}",
            type: "GET",
            data: params,
            success: function(data) {
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                if (chartInstance) {
                    chartInstance.destroy();
                }

                if (data.labels.length === 0) {
                    // Tampilkan pesan "Tidak ada data"
                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Tidak ada data'],
                            datasets: [
                                { label: 'Hadir', data: [0], backgroundColor: 'rgba(200,200,200,0.5)' },
                                { label: 'Terlambat', data: [0], backgroundColor: 'rgba(200,200,200,0.5)' }
                            ]
                        },
                        options: {
                            plugins: {
                                title: { display: true, text: 'Tidak ada data presensi untuk periode ini' }
                            }
                        }
                    });
                    return;
                }

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Hadir',
                                data: data.present,
                                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Terlambat',
                                data: data.late,
                                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Rekap Presensi Harian'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal memuat grafik.'
                });
            }
        });
    }

    // Filter submit
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        if (table) {
            table.ajax.reload();
        }
        loadChart();
    });

    // Export Excel dengan validasi data kosong
   // Export Excel dengan validasi data kosong
$('#exportExcel').on('click', function(e) {
    e.preventDefault();

    const params = new URLSearchParams({
        user_id: $('#filter_teacher').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    });

    // Tampilkan loading
    Swal.fire({
        title: 'Mengekspor...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 🔥 Gunakan fetch untuk menangani response dengan lebih baik
    fetch("{{ route('teacher-attendance-report.export') }}?" + params.toString(), {
        method: 'GET',
        headers: {
            'Accept': 'application/json, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, */*'
        }
    })
    .then(response => {
        // Cek apakah response OK
        if (!response.ok) {
            // Jika status 404 atau 422, coba parse JSON error
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }

        // Cek content type
        const contentType = response.headers.get('Content-Type');
        if (contentType && contentType.includes('application/json')) {
            // Jika response JSON (kemungkinan error)
            return response.json().then(data => {
                if (!data.success) {
                    throw { status: 200, data: data };
                }
                return data;
            });
        }

        // Response berupa file Excel (blob)
        return response.blob().then(blob => {
            return { blob: blob, headers: response.headers };
        });
    })
    .then(result => {
        Swal.close();

        // Jika hasil berupa blob (file)
        if (result.blob) {
            const contentDisposition = result.headers.get('Content-Disposition');
            let filename = 'Rekap_Presensi_Guru.xlsx';
            if (contentDisposition) {
                const match = contentDisposition.match(/filename="(.+?)"/);
                if (match) filename = match[1];
            }

            const link = document.createElement('a');
            link.href = URL.createObjectURL(result.blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File Excel berhasil diunduh.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        // Jika hasil berupa data JSON (success)
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message || 'Data berhasil diekspor.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        Swal.close();
        
        let errorMsg = 'Terjadi kesalahan saat mengekspor data.';
        if (error.data && error.data.message) {
            errorMsg = error.data.message;
        } else if (error.message) {
            errorMsg = error.message;
        }

        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: errorMsg
        });
    });
});

    // Initialize
    initTable();
    loadChart();
});
</script>
@endpush