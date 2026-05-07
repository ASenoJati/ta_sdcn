@extends('layouts.admin.app')

@section('title', 'Manajemen Lokasi')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Lokasi</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lokasi</li>
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
                        <h3 class="card-title">Data Lokasi</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalLocation" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Lokasi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="locationTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Lokasi</th>
                                        <th>Koordinat</th>
                                        <th>Radius</th>
                                        <th>Alamat</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibuat</th>
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

<!-- Modal Form Location -->
<div class="modal fade" id="modalLocation" tabindex="-1" aria-labelledby="modalLocationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLocationLabel">Form Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="locationForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="location_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Kantor Pusat, Gedung A, dll" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Contoh: -6.200000" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Contoh: -6.200000 (Jakarta)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Contoh: 106.816666" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Contoh: 106.816666 (Jakarta)</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="radius_km" class="form-label">Radius (KM) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="radius_km" name="radius_km" placeholder="Contoh: 10" min="1" max="1000" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Jarak radius dalam kilometer</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="default" class="form-label">Jadikan Default</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" class="form-check-input" id="default" name="default" value="1" style="width: 40px; height: 20px;">
                                <label class="form-check-label" for="default">Ya, jadikan lokasi default</label>
                            </div>
                            <small class="text-muted d-block">Hanya satu lokasi yang dapat menjadi default</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Masukkan alamat lengkap lokasi..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Masukkan deskripsi tambahan..."></textarea>
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
                <p>Apakah Anda yakin ingin menghapus lokasi <strong id="location_name"></strong>?</p>
                <p class="text-danger"><small>Perhatian: Lokasi default tidak dapat dihapus!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteLocation()">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set Default -->
<div class="modal fade" id="modalSetDefault" tabindex="-1" aria-labelledby="modalSetDefaultLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSetDefaultLabel">Konfirmasi Set Default</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menjadikan <strong id="default_location_name"></strong> sebagai lokasi default?</p>
                <p class="text-warning"><small>Lokasi default sebelumnya akan dinonaktifkan.</small></p>
                <input type="hidden" id="set_default_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" onclick="setDefaultLocation()">Ya, Jadikan Default</button>
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
    const table = $('#locationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('location.data') }}",
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
            { data: 'name', name: 'name' },
            { data: 'coordinates', name: 'coordinates', orderable: false },
            { data: 'radius_km_formatted', name: 'radius_km' },
            { data: 'address_short', name: 'address' },
            { data: 'default_badge', name: 'default' },
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

    // Submit form via AJAX
    $('#locationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        const id = $('#location_id').val();
        let url, method;
        
        if (id) {
            // Untuk update
            url = "{{ url('admin/location') }}/" + id;
            method = 'POST';
            // Tambahkan _method=PUT untuk Laravel
            if ($('#locationForm input[name="_method"]').length === 0) {
                $('#locationForm').append('<input type="hidden" name="_method" value="PUT">');
            } else {
                $('#locationForm input[name="_method"]').val('PUT');
            }
        } else {
            // Untuk store
            url = "{{ route('location.store') }}";
            method = 'POST';
            // Hapus _method jika ada
            $('#locationForm input[name="_method"]').remove();
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
                    $('#modalLocation').modal('hide');
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

// Function to edit location
function editLocation(id) {
    console.log('Editing location ID:', id);
    
    const url = "{{ url('admin/location') }}/" + id + "/edit";
    
    $.ajax({
        url: url,
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('Edit success:', data);
            
            $('#location_id').val(data.id);
            $('#name').val(data.name);
            $('#latitude').val(data.latitude);
            $('#longitude').val(data.longitude);
            $('#radius_km').val(data.radius_km);
            $('#address').val(data.address);
            $('#description').val(data.description);
            
            // Set checkbox default
            if (data.default == 1) {
                $('#default').prop('checked', true);
            } else {
                $('#default').prop('checked', false);
            }
            
            $('#modalLocationLabel').text('Edit Data Lokasi');
            $('#modalLocation').modal('show');
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

// Function to set default location
function setDefault(id, name) {
    $('#set_default_id').val(id);
    $('#default_location_name').text(name);
    $('#modalSetDefault').modal('show');
}

function setDefaultLocation() {
    const id = $('#set_default_id').val();
    const url = "{{ url('admin/location') }}/" + id + "/set-default";
    
    $.ajax({
        url: url,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalSetDefault').modal('hide');
                $('#locationTable').DataTable().ajax.reload();
                
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

// Function to confirm delete
function confirmDelete(id, name) {
    $('#hapus_id').val(id);
    $('#location_name').text(name);
    $('#modalHapus').modal('show');
}

// Function to delete location
function deleteLocation() {
    const id = $('#hapus_id').val();
    const url = "{{ url('admin/location') }}/" + id;
    
    $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                $('#locationTable').DataTable().ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message,
                    timer: 3000,
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
    $('#locationForm')[0].reset();
    $('#location_id').val('');
    $('#default').prop('checked', false);
    $('#modalLocationLabel').text('Tambah Data Lokasi');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#locationForm input[name="_method"]').remove();
}

// Validasi input latitude dan longitude
$('#latitude, #longitude').on('input', function() {
    // Hanya angka, titik, dan minus yang diperbolehkan
    this.value = this.value.replace(/[^0-9.-]/g, '');
});
</script>
@endpush