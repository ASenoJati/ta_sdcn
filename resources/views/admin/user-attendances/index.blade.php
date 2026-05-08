@extends('layouts.admin.app')

@section('title', 'Presensi Harian Guru')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Presensi Harian Guru</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Presensi Guru</li>
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
                        <h3 class="card-title">Data Presensi Harian Guru</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="userAttendanceTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Guru</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Status</th>
                                        <th>Durasi</th>
                                        <th>Dibuat</th>
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

<!-- Modal Detail Presensi -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="bi bi-calendar-check-fill"></i> Detail Presensi Guru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
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
                <p>Apakah Anda yakin ingin menghapus data presensi:</p>
                <p class="fw-bold text-center" id="attendance_info"></p>
                <p class="text-warning"><small>Perhatian: Tindakan ini tidak dapat dibatalkan!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteAttendance()">Hapus</button>
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
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .attendance-detail-card {
        margin-bottom: 20px;
    }
    .map-container {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
    }
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 14px;
        margin-bottom: 10px;
    }
    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }
    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        cursor: pointer;
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Document ready, initializing DataTable...');
    
    // Inisialisasi DataTable
    const table = $('#userAttendanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('user-attendances.data') }}",
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
            { data: 'user_name', name: 'user.name' },
            { data: 'location_name', name: 'location.name' },
            { data: 'attendance_info', name: 'attendance_date' },
            { data: 'status_info', name: 'check_in_status' },
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
        order: [[6, 'desc']]
    });
});

// Function to view detail
function viewDetail(id) {
    $('#modalDetail').modal('show');
    
    $.ajax({
        url: "{{ url('admin/user-attendances') }}/" + id,
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

// Function to display detail with maps
function displayDetail(data) {
    // Status mapping
    const statusMap = {
        'present': 'Tepat Waktu',
        'late': 'Terlambat',
        'absent': 'Tidak Hadir',
        'on_time': 'Tepat Waktu',
        'early': 'Pulang Awal',
        'late_out': 'Pulang Terlambat'
    };
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-person-badge"></i> Informasi Guru
                    </div>
                    <div class="card-body">
                        <div class="info-label">Nama Guru</div>
                        <div class="info-value"><strong>${data.user.name}</strong></div>
                        
                        <div class="info-label">Email</div>
                        <div class="info-value">${data.user.email}</div>
                        
                        <div class="info-label">Lokasi Presensi</div>
                        <div class="info-value">${data.location ? data.location.name : '-'}</div>
                        
                        ${data.location ? `
                        <div class="info-label">Alamat Lokasi</div>
                        <div class="info-value">${data.location.address || '-'}</div>
                        ` : ''}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-clock-history"></i> Informasi Presensi
                    </div>
                    <div class="card-body">
                        <div class="info-label">Tanggal Presensi</div>
                        <div class="info-value"><strong>${data.attendance_date_formatted}</strong></div>
                        
                        <div class="info-label">Check-in Time</div>
                        <div class="info-value">${data.check_in_time_formatted} ${data.check_in_status_badge}</div>
                        
                        <div class="info-label">Check-out Time</div>
                        <div class="info-value">${data.check_out_time_formatted} ${data.check_out_status_badge || '<span class="badge bg-secondary">Belum Check-out</span>'}</div>
                        
                        <div class="info-label">Durasi Kerja</div>
                        <div class="info-value"><span class="badge bg-primary">${data.work_duration}</span></div>
                        
                        ${data.notes ? `
                        <div class="info-label">Catatan</div>
                        <div class="info-value">${data.notes}</div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-geo-alt-fill"></i> Lokasi Check-in
                    </div>
                    <div class="card-body">
                        <div class="info-label">Koordinat</div>
                        <div class="info-value">
                            Latitude: ${data.check_in_latitude}<br>
                            Longitude: ${data.check_in_longitude}
                        </div>
                        <div id="mapCheckIn" class="map-container"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-geo-alt-fill"></i> Lokasi Check-out
                    </div>
                    <div class="card-body">
                        ${data.check_out_latitude && data.check_out_longitude ? `
                        <div class="info-label">Koordinat</div>
                        <div class="info-value">
                            Latitude: ${data.check_out_latitude}<br>
                            Longitude: ${data.check_out_longitude}
                        </div>
                        <div id="mapCheckOut" class="map-container"></div>
                        ` : '<div class="alert alert-secondary">Belum ada data check-out</div>'}
                    </div>
                </div>
            </div>
        </div>
        
        ${data.image_in || data.image_out ? `
        <div class="row">
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-camera-fill"></i> Foto Check-in
                    </div>
                    <div class="card-body text-center">
                        ${data.image_in ? `
                        <img src="/storage/${data.image_in}" class="image-preview img-fluid" alt="Foto Check-in" onclick="showImageModal(this.src)">
                        ` : '<div class="alert alert-secondary">Tidak ada foto</div>'}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card attendance-detail-card">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-camera-fill"></i> Foto Check-out
                    </div>
                    <div class="card-body text-center">
                        ${data.image_out ? `
                        <img src="/storage/${data.image_out}" class="image-preview img-fluid" alt="Foto Check-out" onclick="showImageModal(this.src)">
                        ` : '<div class="alert alert-secondary">Tidak ada foto</div>'}
                    </div>
                </div>
            </div>
        </div>
        ` : ''}
    `;
    
    $('#detailContent').html(html);
    
    // Initialize maps after content is loaded
    setTimeout(() => {
        initMap('mapCheckIn', data.check_in_latitude, data.check_in_longitude, 'Check-in Location');
        if (data.check_out_latitude && data.check_out_longitude) {
            initMap('mapCheckOut', data.check_out_latitude, data.check_out_longitude, 'Check-out Location');
        }
    }, 100);
}

// Initialize Leaflet map
function initMap(elementId, lat, lng, title) {
    const mapElement = document.getElementById(elementId);
    if (!mapElement) return;
    
    const latitude = parseFloat(lat);
    const longitude = parseFloat(lng);
    
    if (isNaN(latitude) || isNaN(longitude)) {
        mapElement.innerHTML = '<div class="alert alert-danger">Invalid coordinates</div>';
        return;
    }
    
    const map = L.map(elementId).setView([latitude, longitude], 15);
    
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
    
    const marker = L.marker([latitude, longitude]).addTo(map);
    marker.bindPopup(`<b>${title}</b><br>Lat: ${latitude}<br>Lng: ${longitude}`).openPopup();
}

// Show image modal
function showImageModal(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: 'Foto Presensi',
        imageWidth: '80%',
        imageHeight: 'auto',
        confirmButtonText: 'Tutup',
        background: '#000',
        showCloseButton: true
    });
}

// Function to confirm delete
function confirmDelete(id, info) {
    $('#hapus_id').val(id);
    $('#attendance_info').html(info);
    $('#modalHapus').modal('show');
}

// Function to delete attendance
function deleteAttendance() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/user-attendances') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#userAttendanceTable').DataTable().ajax.reload();
                
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