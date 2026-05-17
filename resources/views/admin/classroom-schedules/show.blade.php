@extends('layouts.admin.app')

@section('title', 'Jadwal - ' . $classroom->name)

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">
                    <i class="bi bi-calendar-week-fill me-2"></i>
                    Jadwal Kelas {{ $classroom->name }}
                </h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('classroom-schedules.index') }}">Jadwal per Kelas</a></li>
                    <li class="breadcrumb-item active">{{ $classroom->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
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
                                    <tr><th width="150">Nama Kelas</th><td>: <strong>{{ $classroom->name }}</strong></td></tr>
                                    <tr><th>Deskripsi</th><td>: {{ $classroom->description ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr><th width="150">Dibuat Pada</th><td>: {{ $classroom->created_at->format('d/m/Y H:i') }}</td></tr>
                                    <tr><th>Terakhir Update</th><td>: {{ $classroom->updated_at->format('d/m/Y H:i') }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 mb-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-table me-2"></i> Daftar Jadwal {{ $classroom->name }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSchedule" onclick="resetForm()">
                                <i class="bi bi-plus-circle"></i> Tambah Jadwal
                            </button>
                            <a href="{{ route('classroom-schedules.index') }}" class="btn btn-secondary btn-sm">
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

<!-- Modal Form Schedule -->
<div class="modal fade" id="modalSchedule" tabindex="-1" aria-labelledby="modalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalScheduleLabel">Tambah Jadwal - {{ $classroom->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm">
                @csrf
                <input type="hidden" name="id" id="schedule_id">
                <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Menambahkan jadwal untuk kelas: <strong>{{ $classroom->name }}</strong>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guru <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Pilih Guru</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="subject_id" name="subject_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
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
                            <label class="form-label">Jam Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="lesson_hour_id" name="lesson_hour_id" required>
                                <option value="">Pilih Jam Pelajaran</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-3" id="classroomConflictWarning" style="display: none;"></div>
                    <div class="alert alert-warning mt-3" id="teacherConflictWarning" style="display: none;"></div>
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
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jadwal: <strong id="schedule_info"></strong>?</p>
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
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let dataTable = null;
let hasClassroomConflict = false;
let hasTeacherConflict = false;

$(document).ready(function() {
    loadTeachers();
    loadSubjects();
    loadLessonHours();
    loadScheduleTable();

    $('#user_id, #day, #lesson_hour_id').on('change', checkConflict);

    $('#scheduleForm').on('submit', function(e) {
        e.preventDefault();
        if (hasClassroomConflict || hasTeacherConflict) {
            Swal.fire({ icon: 'error', title: 'Tidak Dapat Menyimpan!', text: 'Terdapat bentrok jadwal!' });
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

        const id = $('#schedule_id').val();
        let url, method, formData;
        
        if (id) {
            // Untuk update
            url = "{{ url('admin/classroom-schedules/schedule') }}/" + id;
            method = 'POST';
            formData = $(this).serialize() + '&_method=PUT';
        } else {
            // Untuk store
            url = "{{ route('classroom-schedules.store-schedule') }}";
            method = 'POST';
            formData = $(this).serialize();
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#modalSchedule').modal('hide');
                    resetForm();
                    loadScheduleTable();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    $('.is-invalid').removeClass('is-invalid');
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                    });
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Silakan periksa form Anda.' });
                } else if (xhr.responseJSON?.type === 'classroom_conflict') {
                    Swal.fire({ icon: 'error', title: 'Bentrok Jadwal - Kelas', text: xhr.responseJSON.message });
                } else if (xhr.responseJSON?.type === 'teacher_conflict') {
                    Swal.fire({ icon: 'error', title: 'Bentrok Jadwal - Guru', text: xhr.responseJSON.message });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message || 'Terjadi kesalahan.' });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function checkConflict() {
    const classroomId = {{ $classroom->id }};
    const teacherId = $('#user_id').val();
    const day = $('#day').val();
    const lessonHourId = $('#lesson_hour_id').val();
    const scheduleId = $('#schedule_id').val();

    hasClassroomConflict = false;
    hasTeacherConflict = false;
    $('#classroomConflictWarning').hide();
    $('#teacherConflictWarning').hide();

    if (classroomId && day && lessonHourId) {
        $.ajax({
            url: "{{ route('classroom-schedules.check-availability') }}",
            type: "POST",
            data: {
                classroom_id: classroomId,
                user_id: teacherId,
                day: day,
                lesson_hour_id: lessonHourId,
                id: scheduleId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (!response.available) {
                    if (response.classroom_conflict) {
                        hasClassroomConflict = true;
                        $('#classroomConflictWarning').html(`<i class="bi bi-exclamation-triangle-fill"></i> Kelas sudah terisi: ${response.classroom_conflict_info.subject} (${response.classroom_conflict_info.lesson_hour})`).show();
                    }
                    if (response.teacher_conflict) {
                        hasTeacherConflict = true;
                        $('#teacherConflictWarning').html(`<i class="bi bi-exclamation-triangle-fill"></i> Guru sudah mengajar: ${response.teacher_conflict_info.classroom} (${response.teacher_conflict_info.lesson_hour})`).show();
                    }
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $('button[type="submit"]').prop('disabled', false);
                }
            }
        });
    } else {
        $('button[type="submit"]').prop('disabled', false);
    }
}

function loadScheduleTable() {
    if (dataTable) dataTable.destroy();
    dataTable = $('#scheduleTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('classroom-schedules.schedule-data', $classroom->id) }}",
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'day_indonesian' },
            { data: 'lesson_time' },
            { data: 'subject_name' },
            { data: 'teacher_name' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            processing: "<div class='spinner-border text-primary'></div>",
            search: "Cari:",
            zeroRecords: "Tidak ada jadwal"
        }
    });
}

function loadTeachers() {
    $.ajax({
        url: "{{ route('classroom-schedules.teachers') }}",
        success: function(data) {
            let select = $('#user_id');
            select.empty().append('<option value="">Pilih Guru</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        }
    });
}

function loadSubjects() {
    $.ajax({
        url: "{{ route('subjects.list') }}",
        success: function(data) {
            let select = $('#subject_id');
            select.empty().append('<option value="">Pilih Mata Pelajaran</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        }
    });
}

function loadLessonHours() {
    $.ajax({
        url: "{{ route('classroom-schedules.lesson-hours') }}",
        success: function(data) {
            let select = $('#lesson_hour_id');
            select.empty().append('<option value="">Pilih Jam Pelajaran</option>');
            $.each(data, function(key, value) {
                select.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        }
    });
}

function editSchedule(id) {
    $.ajax({
        url: "{{ url('admin/classroom-schedules/schedule') }}/" + id + "/edit",
        type: "GET",
        success: function(data) {
            $('#schedule_id').val(data.id);
            $('#user_id').val(data.user_id);
            $('#subject_id').val(data.subject_id);
            $('#day').val(data.day);
            $('#lesson_hour_id').val(data.lesson_hour_id);
            $('#modalScheduleLabel').text('Edit Jadwal - {{ $classroom->name }}');
            $('#modalSchedule').modal('show');
            setTimeout(function() {
                $('#user_id').trigger('change');
            }, 100);
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Data tidak ditemukan.', timer: 2000 });
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
        url: "{{ url('admin/classroom-schedules/schedule') }}/" + id,
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modalHapus').modal('hide');
                loadScheduleTable();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.', timer: 3000 });
        }
    });
}

function resetForm() {
    $('#scheduleForm')[0].reset();
    $('#schedule_id').val('');
    $('#modalScheduleLabel').text('Tambah Jadwal - {{ $classroom->name }}');
    $('.is-invalid').removeClass('is-invalid');
    $('#classroomConflictWarning, #teacherConflictWarning').hide();
    $('button[type="submit"]').prop('disabled', false);
    hasClassroomConflict = false;
    hasTeacherConflict = false;
}
</script>
@endpush