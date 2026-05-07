@extends('layouts.admin.app')

@section('title', 'Manajemen Setting Waktu Presensi')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Setting Waktu Presensi</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Setting Waktu Presensi</li>
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
                        <h3 class="card-title">Data Setting Waktu Presensi</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAttendanceSetting" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Setting
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendanceSettingTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Setting</th>
                                        <th>Waktu Check-in</th>
                                        <th>Waktu Check-out</th>
                                        <th>Grace Period</th>
                                        <th>Penggunaan</th>
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

<!-- Modal Form Attendance Setting -->
<div class="modal fade" id="modalAttendanceSetting" tabindex="-1" aria-labelledby="modalAttendanceSettingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAttendanceSettingLabel">Form Setting Waktu Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="attendanceSettingForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="setting_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Setting <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Standar, Shift Pagi, Shift Siang" required>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Nama unik untuk mengidentifikasi setting waktu presensi</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="check_in_start" class="form-label">Check-in Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="check_in_start" name="check_in_start" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="check_in_end" class="form-label">Check-in Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="check_in_end" name="check_in_end" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Harus setelah waktu Check-in Mulai</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="check_out_start" class="form-label">Check-out Mulai</label>
                            <input type="time" class="form-control" id="check_out_start" name="check_out_start">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Opsional</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="check_out_end" class="form-label">Check-out Selesai</label>
                            <input type="time" class="form-control" id="check_out_end" name="check_out_end">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Harus setelah waktu Check-out Mulai</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="grace_period_minutes" class="form-label">Grace Period (menit)</label>
                        <input type="number" class="form-control" id="grace_period_minutes" name="grace_period_minutes" min="0" max="999" value="0">
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Waktu toleransi keterlambatan dalam menit (0 = tidak ada toleransi)</small>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Check-in: Waktu dimana pengguna dapat melakukan presensi masuk</li>
                            <li>Check-out: Waktu dimana pengguna dapat melakukan presensi pulang</li>
                            <li>Grace Period: Waktu tambahan setelah check-in selesai untuk presensi terlambat</li>
                        </ul>
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
                <p>Apakah Anda yakin ingin menghapus setting waktu presensi <strong id="setting_name"></strong>?</p>
                <p class="text-danger"><small>Perhatian: Setting yang sedang digunakan oleh role tidak dapat dihapus!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteSetting()">Hapus</button>
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

        const table = $('#attendanceSettingTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('attendance-settings.data') }}",
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
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'check_in_range',
                    name: 'check_in_range'
                },
                {
                    data: 'check_out_range',
                    name: 'check_out_range'
                },
                {
                    data: 'grace_period_formatted',
                    name: 'grace_period_minutes'
                },
                {
                    data: 'usage_count',
                    name: 'usage_count',
                    orderable: false
                },
                {
                    data: 'created_at_formatted',
                    name: 'created_at'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }
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
            buttons: [{
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            responsive: true,
            order: [
                [1, 'asc']
            ]
        });

        // Validasi check-out time
        $('#check_out_start, #check_out_end').on('change', function() {
            const start = $('#check_out_start').val();
            const end = $('#check_out_end').val();

            if (start && end && start >= end) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Waktu Check-out Selesai harus setelah Check-out Mulai!',
                    timer: 2000
                });
                $(this).val('');
            }
        });

        // Submit form via AJAX
        $('#attendanceSettingForm').on('submit', function(e) {
            e.preventDefault();

            // Validasi tambahan
            const checkInStart = $('#check_in_start').val();
            const checkInEnd = $('#check_in_end').val();

            if (checkInStart >= checkInEnd) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Waktu Check-in Selesai harus setelah Check-in Mulai!',
                    timer: 3000
                });
                return;
            }

            const checkOutStart = $('#check_out_start').val();
            const checkOutEnd = $('#check_out_end').val();

            if (checkOutStart && checkOutEnd && checkOutStart >= checkOutEnd) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Waktu Check-out Selesai harus setelah Check-out Mulai!',
                    timer: 3000
                });
                return;
            }

            // Disable submit button
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            const id = $('#setting_id').val();
            let url, method;

            if (id) {
                // Untuk update
                url = "{{ url('admin/attendance-setting') }}/" + id;
                method = 'POST';
                // Tambahkan _method=PUT untuk Laravel
                if ($('#attendanceSettingForm input[name="_method"]').length === 0) {
                    $('#attendanceSettingForm').append('<input type="hidden" name="_method" value="PUT">');
                } else {
                    $('#attendanceSettingForm input[name="_method"]').val('PUT');
                }
            } else {
                // Untuk store
                url = "{{ route('attendance-setting.store') }}";
                method = 'POST';
                // Hapus _method jika ada
                $('#attendanceSettingForm input[name="_method"]').remove();
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
                        $('#modalAttendanceSetting').modal('hide');
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

    // Function to edit setting
    function editSetting(id) {
        console.log('Editing setting ID:', id);

        const url = "{{ url('admin/attendance-setting') }}/" + id + "/edit";

        $.ajax({
            url: url,
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                console.log('Edit success:', data);

                $('#setting_id').val(data.id);
                $('#name').val(data.name);
                $('#check_in_start').val(data.check_in_start);
                $('#check_in_end').val(data.check_in_end);
                $('#check_out_start').val(data.check_out_start || '');
                $('#check_out_end').val(data.check_out_end || '');
                $('#grace_period_minutes').val(data.grace_period_minutes);

                $('#modalAttendanceSettingLabel').text('Edit Setting Waktu Presensi');
                $('#modalAttendanceSetting').modal('show');
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
        $('#setting_name').text(name);
        $('#modalHapus').modal('show');
    }

    // Function to delete setting
    function deleteSetting() {
        const id = $('#hapus_id').val();
        const url = "{{ url('admin/attendance-setting') }}/" + id;

        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#modalHapus').modal('hide');
                    $('#attendanceSettingTable').DataTable().ajax.reload();

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
        $('#attendanceSettingForm')[0].reset();
        $('#setting_id').val('');
        $('#grace_period_minutes').val(0);
        $('#modalAttendanceSettingLabel').text('Tambah Setting Waktu Presensi');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#attendanceSettingForm input[name="_method"]').remove();
    }
</script>
@endpush