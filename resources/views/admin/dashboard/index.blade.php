@extends('layouts.admin.app')

@section('title', 'Dashboard Admin')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Dashboard</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        
        <!-- Info Boxes -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['total_teachers'] }}</h3>
                        <p>Total Guru</p>
                    </div>
                    <i class="bi bi-person-badge small-box-icon"></i>
                    <a href="{{ route('user.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                    <div class="inner">
                        <h3>{{ $stats['total_students'] }}</h3>
                        <p>Total Siswa</p>
                    </div>
                    <i class="bi bi-people-fill small-box-icon"></i>
                    <a href="{{ route('students.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['total_classrooms'] }}</h3>
                        <p>Total Kelas</p>
                    </div>
                    <i class="bi bi-building-fill small-box-icon"></i>
                    <a href="{{ route('classrooms.index') }}" class="small-box-footer link-dark">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['total_subjects'] }}</h3>
                        <p>Total Mata Pelajaran</p>
                    </div>
                    <i class="bi bi-book-fill small-box-icon"></i>
                    <a href="{{ route('subjects.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Second Row Info Boxes -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_locations'] }}</h3>
                        <p>Total Lokasi</p>
                    </div>
                    <i class="bi bi-geo-alt-fill small-box-icon"></i>
                    <a href="{{ route('location.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-secondary">
                    <div class="inner">
                        <h3>{{ $stats['total_lesson_hours'] }}</h3>
                        <p>Jam Pelajaran</p>
                    </div>
                    <i class="bi bi-clock-fill small-box-icon"></i>
                    <a href="{{ route('lesson-hours.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-dark">
                    <div class="inner">
                        <h3>{{ $stats['total_teaching_schedules'] }}</h3>
                        <p>Jadwal Aktif</p>
                    </div>
                    <i class="bi bi-calendar-week-fill small-box-icon"></i>
                    <a href="{{ route('teaching-schedules.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-purple" style="background-color: #6f42c1;">
                    <div class="inner">
                        <h3>{{ $stats['total_attendance_settings'] }}</h3>
                        <p>Setting Presensi</p>
                    </div>
                    <i class="bi bi-clock-history small-box-icon"></i>
                    <a href="{{ route('attendance-setting.index') }}" class="small-box-footer link-light">
                        Detail <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-graph-up me-2"></i> Presensi Guru 7 Hari Terakhir
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-pie-chart-fill me-2"></i> Status Presensi Hari Ini
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <div class="alert alert-success mb-0">
                                    <h5>{{ $attendanceStatusChart['present'] }}</h5>
                                    <small>Tepat Waktu</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="alert alert-warning mb-0">
                                    <h5>{{ $attendanceStatusChart['late'] }}</h5>
                                    <small>Terlambat</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="alert alert-danger mb-0">
                                    <h5>{{ $attendanceStatusChart['absent'] }}</h5>
                                    <small>Tidak Hadir</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Row Charts -->
        <div class="row">
            <div class="col-lg-6 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-journal-bookmark-fill me-2"></i> Jurnal Pembelajaran per Bulan
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="journalChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-trophy-fill me-2"></i> Top 5 Mata Pelajaran
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="topSubjectsChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Today's Schedule & Recent Activities -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-calendar-day-fill me-2"></i> Jadwal Hari Ini - {{ Carbon\Carbon::now()->format('l, d/m/Y') }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge bg-primary">{{ $todaySchedules->count() }} Jadwal</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todaySchedules as $schedule)
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $schedule->lessonHour->session }} ({{ substr($schedule->lessonHour->start_time, 0, 5) }})
                                            </span>
                                        </td>
                                        <td>{{ $schedule->subject->name }}</td>
                                        <td>{{ $schedule->classroom->name }}</td>
                                        <td>{{ $schedule->teacher->name ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada jadwal hari ini</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('teaching-schedules.index') }}" class="btn btn-primary btn-sm">
                            Lihat Semua Jadwal <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-activity me-2"></i> Aktivitas Terbaru
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                            <div class="list-group-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-{{ $activity['color'] }} rounded-circle p-2 text-white" style="width: 40px; height: 40px; text-align: center;">
                                            <i class="bi {{ $activity['icon'] }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                        <p class="mb-1 small">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="list-group-item text-center">Belum ada aktivitas</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Location Map Row -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-map-fill me-2"></i> Lokasi Presensi Guru Hari Ini
                        </h3>
                        <div class="card-tools">
                            <span class="badge bg-info">{{ $attendanceLocations->count() }} Lokasi</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="attendanceMap" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats Cards -->
        <div class="row mt-4 mb-2">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Presensi Hari Ini</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Check-in:</span>
                            <span class="badge bg-success fs-6">{{ $attendanceStats['checked_in'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Check-out:</span>
                            <span class="badge bg-info fs-6">{{ $attendanceStats['checked_out'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Tepat Waktu:</span>
                            <span class="badge bg-success fs-6">{{ $attendanceStats['on_time'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Terlambat:</span>
                            <span class="badge bg-warning fs-6">{{ $attendanceStats['late'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Jurnal</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Total Jurnal:</span>
                            <span class="badge bg-primary fs-6">{{ $journalStats['total_journals'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Bulan Ini:</span>
                            <span class="badge bg-success fs-6">{{ $journalStats['this_month_journals'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Minggu Ini:</span>
                            <span class="badge bg-info fs-6">{{ $journalStats['this_week_journals'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Info Sistem</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Versi Aplikasi:</span>
                            <span class="badge bg-secondary fs-6">v1.0.0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Laravel Version:</span>
                            <span class="badge bg-secondary fs-6">{{ app()->version() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>PHP Version:</span>
                            <span class="badge bg-secondary fs-6">{{ phpversion() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<!--end::App Content-->
@endsection

@push('styles')
<!-- Chart.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
    .small-box-icon {
        font-size: 4.5rem;
        position: absolute;
        right: 15px;
        top: 15px;
        opacity: 0.3;
        transition: transform 0.3s ease;
    }
    .small-box:hover .small-box-icon {
        transform: scale(1.1);
    }
    .text-bg-purple {
        background-color: #6f42c1 !important;
        color: white !important;
    }
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    
    // Attendance Chart (Bar Chart)
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($attendanceChart['labels']) !!},
            datasets: [
                {
                    label: 'Check-in',
                    data: {!! json_encode($attendanceChart['check_in']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: '#28a745',
                    borderWidth: 1
                },
                {
                    label: 'Check-out',
                    data: {!! json_encode($attendanceChart['check_out']) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: '#17a2b8',
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
    
    // Attendance Status Chart (Pie Chart)
    const statusCtx = document.getElementById('attendanceStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'],
            datasets: [{
                data: [
                    {{ $attendanceStatusChart['present'] }}, 
                    {{ $attendanceStatusChart['late'] }}, 
                    {{ $attendanceStatusChart['absent'] }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    // Journal Chart (Line Chart)
    const journalCtx = document.getElementById('journalChart').getContext('2d');
    new Chart(journalCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($journalChart['labels']) !!},
            datasets: [{
                label: 'Jumlah Jurnal',
                data: {!! json_encode($journalChart['data']) !!},
                fill: true,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: '#007bff',
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
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
    
    // Top Subjects Chart (Horizontal Bar Chart)
    const subjectsCtx = document.getElementById('topSubjectsChart').getContext('2d');
    new Chart(subjectsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSubjects->pluck('name')) !!},
            datasets: [{
                label: 'Jumlah Jadwal',
                data: {!! json_encode($topSubjects->pluck('total')) !!},
                backgroundColor: 'rgba(111, 66, 193, 0.7)',
                borderColor: '#6f42c1',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Initialize Map
    const locations = {!! json_encode($attendanceLocations) !!};
    
    if (locations.length > 0 && document.getElementById('attendanceMap')) {
        // Calculate center based on first location or default
        let centerLat = -6.200000;
        let centerLng = 106.816666;
        
        if (locations.length > 0) {
            const totalLat = locations.reduce((sum, loc) => sum + parseFloat(loc.latitude), 0);
            const totalLng = locations.reduce((sum, loc) => sum + parseFloat(loc.longitude), 0);
            centerLat = totalLat / locations.length;
            centerLng = totalLng / locations.length;
        }
        
        const map = L.map('attendanceMap').setView([centerLat, centerLng], 12);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);
        
        // Add markers
        locations.forEach(location => {
            const statusColor = location.status === 'present' ? 'green' : (location.status === 'late' ? 'orange' : 'red');
            const markerHtml = `
                <div style="background-color: ${statusColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>
            `;
            
            const customIcon = L.divIcon({
                html: markerHtml,
                className: 'custom-marker',
                iconSize: [20, 20],
                popupAnchor: [0, -10]
            });
            
            const marker = L.marker([parseFloat(location.latitude), parseFloat(location.longitude)], { icon: customIcon })
                .addTo(map)
                .bindPopup(`
                    <b>${location.name}</b><br>
                    Waktu: ${location.time}<br>
                    Status: ${location.status === 'present' ? 'Tepat Waktu' : (location.status === 'late' ? 'Terlambat' : '-')}
                `);
        });
    } else if (document.getElementById('attendanceMap')) {
        document.getElementById('attendanceMap').innerHTML = '<div class="alert alert-info text-center h-100 d-flex align-items-center justify-content-center">Belum ada data presensi hari ini</div>';
    }
    
    // Auto refresh charts every 30 seconds
    setInterval(function() {
        $.ajax({
            url: "{{ route('admin.dashboard.charts') }}",
            type: "GET",
            success: function(response) {
                console.log('Charts refreshed');
                // Update charts if needed
            }
        });
    }, 30000);
    
});
</script>
@endpush