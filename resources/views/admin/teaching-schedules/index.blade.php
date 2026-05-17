@extends('layouts.admin.app')

@section('title', 'Manajemen Jadwal Pembelajaran')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Jadwal Pembelajaran</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jadwal Pembelajaran</li>
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
                        <h3 class="card-title">Data Jadwal Pembelajaran</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTeachingSchedule" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Jadwal
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="teachingScheduleTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Guru</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Hari</th>
                                        <th>Jam Pelajaran</th>
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

<!-- Modal Form Teaching Schedule -->
<div class="modal fade" id="modalTeachingSchedule" tabindex="-1" aria-labelledby="modalTeachingScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTeachingScheduleLabel">Form Jadwal Pembelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="teachingScheduleForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="schedule_id">
                <div class="modal-body">
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
                            <label for="classroom_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" id="classroom_id" name="classroom_id" required>
                                <option value="">Pilih Kelas</option>
                                <!-- Data kelas akan diisi via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
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
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
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

                    <div class="alert alert-success mt-3" id="noConflictWarning" style="display: none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>Jadwal tersedia!</strong> Tidak ada bentrok jadwal.
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

        // Load data for dropdowns
        loadTeachers();
        loadSubjects();
        loadClassrooms();
        loadLessonHours();

        // Inisialisasi DataTable
        const table = $('#teachingScheduleTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('teaching-schedules.data') }}",
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
                    data: 'teacher_name',
                    name: 'teacher_name'
                },
                {
                    data: 'subject_name',
                    name: 'subject.name'
                },
                {
                    data: 'classroom_name',
                    name: 'classroom.name'
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
                [4, 'asc'],
                [2, 'asc']
            ]
        });

        // Variable to store conflict status
        let hasClassroomConflict = false;
        let hasTeacherConflict = false;
        let classroomConflictInfo = null;
        let teacherConflictInfo = null;

        // Check schedule conflict on change
        function checkConflict() {
            const classroomId = $('#classroom_id').val();
            const teacherId = $('#user_id').val();
            const day = $('#day').val();
            const lessonHourId = $('#lesson_hour_id').val();
            const scheduleId = $('#schedule_id').val();

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

        $('#classroom_id, #user_id, #day, #lesson_hour_id').on('change', checkConflict);

        // Submit form via AJAX
        $('#teachingScheduleForm').on('submit', function(e) {
            e.preventDefault();

            // Validasi semua field required
            const userId = $('#user_id').val();
            const subjectId = $('#subject_id').val();
            const classroomId = $('#classroom_id').val();
            const lessonHourId = $('#lesson_hour_id').val();
            const day = $('#day').val();

            if (!userId || !subjectId || !classroomId || !lessonHourId || !day) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Semua field harus diisi!',
                    timer: 3000
                });
                return;
            }

            // Final check before submit
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

            // Disable submit button
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            const id = $('#schedule_id').val();
            let url, method;

            // Ambil semua data form dengan benar
            const formData = {
                user_id: userId,
                subject_id: subjectId,
                classroom_id: classroomId,
                lesson_hour_id: lessonHourId,
                day: day,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if (id) {
                url = "{{ url('admin/teaching-schedules') }}/" + id;
                method = 'POST';
                formData._method = 'PUT';
            } else {
                url = "{{ route('teaching-schedules.store') }}";
                method = 'POST';
            }

            console.log('Submitting data:', formData);

            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalTeachingSchedule').modal('hide');
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
                    console.error('Error response:', xhr);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');

                        if (errors) {
                            $.each(errors, function(key, value) {
                                const input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);
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

    // Function to load teachers
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

    // Function to load subjects
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

    // Function to load classrooms
    function loadClassrooms() {
        $.ajax({
            url: "{{ route('classrooms.list') }}",
            type: "GET",
            success: function(data) {
                const select = $('#classroom_id');
                select.empty();
                select.append('<option value="">Pilih Kelas</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    }

    // Function to load lesson hours
    function loadLessonHours() {
        $.ajax({
            url: "{{ route('teaching-schedules.lesson-hours') }}",
            type: "GET",
            success: function(data) {
                console.log('Lesson hours loaded:', data);
                const select = $('#lesson_hour_id');
                select.empty();
                select.append('<option value="">Pilih Jam Pelajaran</option>');
                $.each(data, function(key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            },
            error: function(xhr) {
                console.error('Error loading lesson hours:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data jam pelajaran.',
                    timer: 3000
                });
            }
        });
    }

    // Function to edit schedule
    function editSchedule(id) {
        const url = "{{ url('admin/teaching-schedules') }}/" + id + "/edit";

        $.ajax({
            url: url,
            type: "GET",
            success: function(data) {
                console.log('Edit data:', data);

                $('#schedule_id').val(data.id);
                $('#user_id').val(data.user_id);
                $('#subject_id').val(data.subject_id);
                $('#classroom_id').val(data.classroom_id);
                $('#day').val(data.day);
                $('#lesson_hour_id').val(data.lesson_hour_id);

                $('#modalTeachingScheduleLabel').text('Edit Jadwal Pembelajaran');
                $('#modalTeachingSchedule').modal('show');

                // Trigger conflict check
                setTimeout(() => {
                    $('#classroom_id').trigger('change');
                }, 100);
            },
            error: function(xhr) {
                console.error('Edit error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data tidak ditemukan.',
                    timer: 2000
                });
            }
        });
    }

    // Function to confirm delete
    function confirmDelete(id, info) {
        $('#hapus_id').val(id);
        $('#schedule_info').text(info);
        $('#modalHapus').modal('show');
    }

    // Function to delete schedule
    function deleteSchedule() {
        const id = $('#hapus_id').val();
        const url = "{{ url('admin/teaching-schedules') }}/" + id;

        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#modalHapus').modal('hide');
                    $('#teachingScheduleTable').DataTable().ajax.reload();

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
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan.',
                    timer: 3000
                });
            }
        });
    }

    // Reset form
    function resetForm() {
        $('#teachingScheduleForm')[0].reset();
        $('#schedule_id').val('');
        $('#modalTeachingScheduleLabel').text('Tambah Jadwal Pembelajaran');
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

        // Reset select boxes
        $('#user_id').val('');
        $('#subject_id').val('');
        $('#classroom_id').val('');
        $('#day').val('');
        $('#lesson_hour_id').val('');
    }
</script>
@endpush