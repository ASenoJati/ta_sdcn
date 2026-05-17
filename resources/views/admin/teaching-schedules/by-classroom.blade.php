@extends('layouts.admin.app')

@section('title', 'Jadwal Pembelajaran - ' . $classroom->name)

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">
                    <i class="bi bi-calendar-week-fill me-2"></i>
                    Jadwal Pembelajaran - {{ $classroom->name }}
                </h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teaching-schedules.index') }}">Jadwal Pembelajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $classroom->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!--end::App Content Header-->

<!--begin::App Content-->
<div class="app-content">
    <div class="container-fluid">
        <!-- Info Kelas -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-building me-2"></i> Informasi Kelas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Nama Kelas</th>
                                        <td>: <strong>{{ $classroom->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>: {{ $classroom->description ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Dibuat Pada</th>
                                        <td>: {{ $classroom->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terakhir Update</th>
                                        <td>: {{ $classroom->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-table me-2"></i> Daftar Jadwal {{ $classroom->name }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTeachingSchedule" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Jadwal
                            </button>
                            <a href="{{ route('teaching-schedules.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="scheduleTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Hari</th>
                                        <th>Jam Pelajaran</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th width="10%">Aksi</th>
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

<!-- Modal Form Teaching Schedule -->
<div class="modal fade" id="modalTeachingSchedule" tabindex="-1" aria-labelledby="modalTeachingScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTeachingScheduleLabel">Form Jadwal Pembelajaran - {{ $classroom->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="teachingScheduleForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="schedule_id">
                <input type="hidden" name="classroom_id" id="classroom_id" value="{{ $classroom->id }}">
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Menambahkan jadwal untuk kelas: <strong>{{ $classroom->name }}</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Guru <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Pilih Guru</option>
                                <!-- Data guru akan diisi via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <!-- Data mata pelajaran akan diisi via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="day" class="form-label">Hari <span class="text-danger">*</span></label>
                            <select class="form-select" id="day" name="day" required>
                                <option value="">Pilih Hari</option>
                                <option value="Monday">Senin</option>
                                <option value="Tuesday">Selasa</option>
                                <option value="Wednesday">Rabu</option>
                                <option value="Thursday">Kamis</option>
                                <option value="Friday">Jumat</option>
                                <option value="Saturday">Sabtu</option>
                                <option value="Sunday">Minggu</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lesson_hour_id" class="form-label">Jam Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="lesson_hour_id" name="lesson_hour_id" required>
                                <option value="">Pilih Jam Pelajaran</option>
                                <!-- Data jam pelajaran akan diisi via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Warning untuk konflik kelas -->
                    <div class="alert alert-danger mt-3" id="classroomConflictWarning" style="display: none;">
                    </div>

                    <!-- Warning untuk konflik guru -->
                    <div class="alert alert-warning mt-3" id="teacherConflictWarning" style="display: none;">
                    </div>

                    <div class="alert alert-info mt-3" id="conflictWarning" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Perhatian!</strong> Terdapat bentrok jadwal. Silakan perbaiki sebelum menyimpan.
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Setiap kelas tidak boleh memiliki jadwal yang bentrok (hari dan jam pelajaran yang sama)</li>
                            <li>Pastikan guru yang dipilih memiliki role sebagai teacher</li>
                            <li>Jam pelajaran harus sesuai dengan sesi yang sudah ditentukan</li>
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
                <p>Apakah Anda yakin ingin menghapus jadwal: <strong id="schedule_info"></strong>?</p>
                <p class="text-warning"><small>Perhatian: Tindakan ini tidak dapat dibatalkan!</small></p>
                <input type="hidden" id="hapus_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="deleteSchedule()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
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
    let dataTable = null;
    let hasClassroomConflict = false;
    let hasTeacherConflict = false;
    let classroomConflictInfo = null;
    let teacherConflictInfo = null;

    $(document).ready(function() {
        // Load data for dropdowns
        loadTeachers();
        loadSubjects();
        loadLessonHours();

        // Inisialisasi DataTable
        dataTable = $('#scheduleTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('teaching-schedules.by-classroom.data', $classroom->id) }}",
                type: "GET",
                error: function(xhr) {
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
                    data: 'day_indonesian',
                    name: 'day'
                },
                {
                    data: 'lesson_time',
                    name: 'lesson_hour.session'
                },
                {
                    data: 'subject_name',
                    name: 'subject.name'
                },
                {
                    data: 'teacher_name',
                    name: 'teacher.name'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                processing: "<div class='spinner-border text-primary' role='status'></div>",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ada data ditemukan",
                emptyTable: "Tidak ada jadwal untuk kelas ini",
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
                [1, 'asc'],
                [2, 'asc']
            ]
        });

        // Check schedule conflict on change
        function checkConflict() {
            const teacherId = $('#user_id').val();
            const day = $('#day').val();
            const lessonHourId = $('#lesson_hour_id').val();
            const scheduleId = $('#schedule_id').val();
            const classroomId = {
                {
                    $classroom - > id
                }
            };

            // Reset conflict status
            hasClassroomConflict = false;
            hasTeacherConflict = false;
            classroomConflictInfo = null;
            teacherConflictInfo = null;

            // Hide all warnings
            $('#classroomConflictWarning').hide();
            $('#teacherConflictWarning').hide();
            $('#conflictWarning').hide();

            if (classroomId && day && lessonHourId) {
                $.ajax({
                    url: "{{ route('teaching-schedules.check-availability') }}",
                    type: "POST",
                    data: {
                        classroom_id: classroomId,
                        user_id: teacherId,
                        day: day,
                        lesson_hour_id: lessonHourId,
                        id: scheduleId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (!response.available) {
                            if (response.classroom_conflict) {
                                hasClassroomConflict = true;
                                classroomConflictInfo = response.classroom_conflict_info;
                                const info = response.classroom_conflict_info;
                                $('#classroomConflictWarning').html(`
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>⚠️ PERINGATAN! Kelas Sudah Terisi!</strong><br>
                                    Kelas ini sudah memiliki jadwal pada hari dan jam yang sama:<br>
                                    • Mata Pelajaran: <strong>${info.subject}</strong><br>
                                    • Guru: <strong>${info.teacher}</strong><br>
                                    • Jam: <strong>${info.lesson_hour}</strong>
                                `);
                                $('#classroomConflictWarning').show();
                            }

                            if (response.teacher_conflict) {
                                hasTeacherConflict = true;
                                teacherConflictInfo = response.teacher_conflict_info;
                                const info = response.teacher_conflict_info;
                                $('#teacherConflictWarning').html(`
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>⚠️ PERINGATAN! Guru Sudah Mengajar!</strong><br>
                                    Guru ini sudah mengajar pada hari dan jam yang sama:<br>
                                    • Kelas: <strong>${info.classroom}</strong><br>
                                    • Mata Pelajaran: <strong>${info.subject}</strong><br>
                                    • Jam: <strong>${info.lesson_hour}</strong>
                                `);
                                $('#teacherConflictWarning').show();
                            }

                            if (response.classroom_conflict || response.teacher_conflict) {
                                $('#conflictWarning').show();
                            }

                            $('button[type="submit"]').prop('disabled', true);
                        } else {
                            $('button[type="submit"]').prop('disabled', false);
                        }
                    }
                });
            } else {
                $('#classroomConflictWarning').hide();
                $('#teacherConflictWarning').hide();
                $('#conflictWarning').hide();
                $('button[type="submit"]').prop('disabled', false);
            }
        }

        $('#user_id, #day, #lesson_hour_id').on('change', checkConflict);

        // Submit form
        $('#teachingScheduleForm').on('submit', function(e) {
            e.preventDefault();

            const userId = $('#user_id').val();
            const subjectId = $('#subject_id').val();
            const lessonHourId = $('#lesson_hour_id').val();
            const day = $('#day').val();

            if (!userId || !subjectId || !lessonHourId || !day) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Semua field harus diisi!',
                    timer: 3000
                });
                return;
            }

            if (hasClassroomConflict || hasTeacherConflict) {
                let errorMessage = 'Tidak dapat menyimpan jadwal karena:\n';
                if (hasClassroomConflict && classroomConflictInfo) {
                    errorMessage += `\n• Kelas sudah terisi: ${classroomConflictInfo.subject} (${classroomConflictInfo.lesson_hour})`;
                }
                if (hasTeacherConflict && teacherConflictInfo) {
                    errorMessage += `\n• Guru sudah mengajar: ${teacherConflictInfo.classroom} (${teacherConflictInfo.lesson_hour})`;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Dapat Menyimpan!',
                    html: errorMessage.replace(/\n/g, '<br>'),
                    confirmButtonText: 'Mengerti'
                });
                return;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            const id = $('#schedule_id').val();
            const url = id ? "{{ url('admin/teaching-schedules') }}/" + id : "{{ route('teaching-schedules.store') }}";

            const formData = {
                user_id: userId,
                subject_id: subjectId,
                classroom_id: {
                    {
                        $classroom - > id
                    }
                },
                lesson_hour_id: lessonHourId,
                day: day,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if (id) {
                formData._method = 'PUT';
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#modalTeachingSchedule').modal('hide');
                        resetForm();
                        dataTable.ajax.reload();

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
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');

                        if (errors) {
                            $.each(errors, function(key, value) {
                                $('[name="' + key + '"]').addClass('is-invalid').siblings('.invalid-feedback').text(value[0]);
                            });
                        }

                        let errorTitle = 'Validasi Gagal';
                        let errorMessage = xhr.responseJSON.message || 'Silakan periksa kembali form Anda.';

                        if (xhr.responseJSON.type === 'classroom_conflict') {
                            errorTitle = '⚠️ Bentrok Jadwal - Kelas';
                            errorMessage = 'Kelas ini sudah memiliki mata pelajaran pada hari dan jam yang sama!';
                        } else if (xhr.responseJSON.type === 'teacher_conflict') {
                            errorTitle = '⚠️ Bentrok Jadwal - Guru';
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: errorMessage,
                            timer: 4000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.',
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

    function loadTeachers() {
        $.ajax({
            url: "{{ route('teaching-schedules.teachers') }}",
            type: "GET",
            success: function(data) {
                const select = $('#user_id');
                select.empty();
                select.append('<option value="">Pilih Guru</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    }

    function loadSubjects() {
        $.ajax({
            url: "{{ route('subjects.list') }}",
            type: "GET",
            success: function(data) {
                const select = $('#subject_id');
                select.empty();
                select.append('<option value="">Pilih Mata Pelajaran</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    }

    function loadLessonHours() {
        $.ajax({
            url: "{{ route('teaching-schedules.lesson-hours') }}",
            type: "GET",
            success: function(data) {
                const select = $('#lesson_hour_id');
                select.empty();
                select.append('<option value="">Pilih Jam Pelajaran</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    }

    function editSchedule(id) {
        $.ajax({
            url: "{{ url('admin/teaching-schedules') }}/" + id + "/edit",
            type: "GET",
            success: function(data) {
                $('#schedule_id').val(data.id);
                $('#user_id').val(data.user_id);
                $('#subject_id').val(data.subject_id);
                $('#day').val(data.day);
                $('#lesson_hour_id').val(data.lesson_hour_id);

                $('#modalTeachingScheduleLabel').text('Edit Jadwal Pembelajaran - {{ $classroom->name }}');
                $('#modalTeachingSchedule').modal('show');

                setTimeout(() => {
                    $('#user_id').trigger('change');
                }, 100);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data tidak ditemukan.',
                    timer: 2000
                });
            }
        });
    }

    function confirmDelete(id, info) {
        $('#hapus_id').val(id);
        $('#schedule_info').text(info);
        $('#modalHapus').modal('show');
    }

    function deleteSchedule() {
        const id = $('#hapus_id').val();
        $.ajax({
            url: "{{ url('admin/teaching-schedules') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#modalHapus').modal('hide');
                    dataTable.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan.',
                    timer: 3000
                });
            }
        });
    }

    function resetForm() {
        $('#teachingScheduleForm')[0].reset();
        $('#schedule_id').val('');
        $('#modalTeachingScheduleLabel').text('Form Jadwal Pembelajaran - {{ $classroom->name }}');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#teachingScheduleForm input[name="_method"]').remove();
        $('#classroomConflictWarning').hide();
        $('#teacherConflictWarning').hide();
        $('#conflictWarning').hide();
        $('button[type="submit"]').prop('disabled', false);
        hasClassroomConflict = false;
        hasTeacherConflict = false;
        classroomConflictInfo = null;
        teacherConflictInfo = null;
    }
</script>
@endpush