@extends('layouts.admin.app')

@section('title', 'Detail Jurnal Pembelajaran')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Detail Jurnal Pembelajaran</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teaching-journals.index') }}">Jurnal Pembelajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
            <div class="col-md-8">
                <!-- Card Informasi Jurnal -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-journal-text me-2"></i> Informasi Jurnal
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Hari / Tanggal</th>
                                <td>{{ $journal->day_name }} - {{ $journal->date_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <td>{{ $journal->teachingSchedule->subject->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td>{{ $journal->teachingSchedule->classroom->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Guru</th>
                                <td>{{ $journal->teachingSchedule->teacher->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jam Pelajaran</th>
                                <td>
                                    @if($journal->teachingSchedule && $journal->teachingSchedule->lessonHour)
                                    {{ \Carbon\Carbon::parse($journal->teachingSchedule->lessonHour->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($journal->teachingSchedule->lessonHour->end_time)->format('H:i') }}
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Materi</th>
                                <td>{{ $journal->material ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Refleksi</th>
                                <td>{{ $journal->reflection ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td>{{ $journal->created_at->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diupdate</th>
                                <td>{{ $journal->updated_at->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('teaching-journals.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-danger float-end" onclick="confirmDelete({{ $journal->id }})">
                            <i class="bi bi-trash me-1"></i> Hapus Jurnal
                        </button>
                    </div>
                </div>
            </div>

         <div class="col-md-4">
    <!-- Card Ringkasan Presensi -->
    <div class="card card-success card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title fw-bold text-dark mb-0">
                <i class="bi bi-clipboard-check me-2"></i> Ringkasan Presensi
            </h3>
        </div>
        <div class="card-body">
            @php
            $summary = $journal->attendance_summary;
            @endphp
            <div class="row text-center g-2">
                <!-- Hadir: Latar Hijau Gelap, Teks Putih Terang -->
                <div class="col-6 mb-2">
                    <div class="info-box bg-success text-white">
                        <span class="info-box-icon text-white"><i class="bi bi-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text fw-semibold">Hadir</span>
                            <span class="info-box-number fw-bold fs-5">{{ $summary['hadir'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Izin: Latar Kuning Terang, Teks Gelap/Hitam -->
                <div class="col-6 mb-2">
                    <div class="info-box bg-warning text-dark">
                        <span class="info-box-icon text-dark"><i class="bi bi-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text fw-semibold">Izin</span>
                            <span class="info-box-number fw-bold fs-5">{{ $summary['izin'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sakit: Latar Biru Gelap, Teks Putih Terang -->
                <div class="col-6 mb-2">
                    <div class="info-box bg-info text-white">
                        <span class="info-box-icon text-white"><i class="bi bi-heart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text fw-semibold">Sakit</span>
                            <span class="info-box-number fw-bold fs-5">{{ $summary['sakit'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Alpa: Latar Merah Gelap, Teks Putih Terang -->
                <div class="col-6 mb-2">
                    <div class="info-box bg-danger text-white">
                        <span class="info-box-icon text-white"><i class="bi bi-x-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text fw-semibold">Alpa</span>
                            <span class="info-box-number fw-bold fs-5">{{ $summary['alpa'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="text-center text-dark">
                <strong class="fs-6 fw-bold">Total Siswa: {{ $summary['total'] ?? 0 }}</strong>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Card Manajemen Materi -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-folder2-open me-2"></i> Materi Pembelajaran
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalMaterial">
                                <i class="bi bi-plus-circle"></i> Tambah Materi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($journal->materials->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Tipe</th>
                                        <th>File / Link</th>
                                        <th>Untuk Siswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($journal->materials as $index => $material)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $material->title }}</td>
                                        <td>
                                            @if($material->type === 'file')
                                            <span class="badge bg-info">File</span>
                                            @else
                                            <span class="badge bg-success">Link</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($material->type === 'file')
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                            @else
                                            <a href="{{ $material->url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-link-45deg"></i> Buka Link
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($material->students->count() > 0)
                                            <span class="badge bg-warning text-dark">
                                                {{ $material->students->count() }} siswa
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showStudents({{ $material->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @else
                                            <span class="badge bg-success">Semua Siswa</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteMaterial({{ $journal->id }}, {{ $material->id }})">
                                                         <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">Belum ada materi yang ditambahkan.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Presensi Siswa -->
       <!-- Tabel Daftar Presensi Siswa (Hanya yang Tidak Hadir) -->
<div class="row">
    <div class="col-12 mt-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-people me-2"></i> Daftar Siswa Tidak Hadir
                    <span class="badge bg-warning ms-2">{{ $filteredAttendances->count() }} Siswa</span>
                </h3>
            </div>
            <div class="card-body">
                @if($filteredAttendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Waktu Presensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredAttendances as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attendance->student->nis ?? '-' }}</td>
                                <td>{{ $attendance->student->name ?? '-' }}</td>
                                <td>
                                    @php
                                    $statusMap = [
                                    'izin' => 'warning',
                                    'sakit' => 'info',
                                    'alpa' => 'danger'
                                    ];
                                    $labelMap = [
                                    'izin' => 'Izin',
                                    'sakit' => 'Sakit',
                                    'alpa' => 'Alpa'
                                    ];
                                    @endphp
                                    <span class="badge bg-{{ $statusMap[$attendance->status] ?? 'secondary' }}">
                                        {{ $labelMap[$attendance->status] ?? $attendance->status }}
                                    </span>
                                </td>
                                <td>{{ $attendance->created_at ? $attendance->created_at->translatedFormat('d F Y H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Semua siswa hadir!</strong> Tidak ada siswa yang izin, sakit, atau alpa.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
    </div>
</div>
<!--end::App Content-->

<!-- Modal Tambah Materi -->
<div class="modal fade" id="modalMaterial" tabindex="-1" aria-labelledby="modalMaterialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalMaterialLabel">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Materi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('teaching-journals.materials.store', $journal->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Materi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Materi <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="typeFile" value="file" checked>
                            <label class="form-check-label" for="typeFile">Upload File</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="typeLink" value="link">
                            <label class="form-check-label" for="typeLink">Link URL</label>
                        </div>
                    </div>
                    <div class="mb-3" id="fileInputGroup">
                        <label for="file" class="form-label">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept="*/*">
                        <small class="text-muted">Maksimal 10MB. Semua format diperbolehkan.</small>
                    </div>
                    <div class="mb-3 d-none" id="linkInputGroup">
                        <label for="link_url" class="form-label">Link URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="link_url" name="url" placeholder="https://example.com/materi">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kirim ke Siswa <span class="text-danger">*</span></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="student_option" id="allStudents" value="all" checked>
                            <label class="form-check-label" for="allStudents">Semua Siswa di Kelas Ini</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="student_option" id="selectedStudents" value="selected">
                            <label class="form-check-label" for="selectedStudents">Pilih Siswa Tertentu</label>
                        </div>
                        <div id="studentSelectionContainer" class="d-none">
                            <div class="border rounded p-3">
                                <div class="row">
                                    @foreach($classroomStudents as $student)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                            <label class="form-check-label" for="student_{{ $student->id }}">
                                                {{ $student->name }} ({{ $student->nis }})
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-box {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .info-box .info-box-icon {
        font-size: 24px;
        display: block;
        text-align: center;
    }

    .info-box .info-box-content {
        text-align: center;
    }

    .info-box .info-box-number {
        font-size: 20px;
        font-weight: bold;
    }

    .info-box.bg-success {
        background: #d4edda;
        color: #155724;
    }

    .info-box.bg-warning {
        background: #fff3cd;
        color: #856404;
    }

    .info-box.bg-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .info-box.bg-danger {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Konfirmasi hapus jurnal
    function confirmDelete(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus jurnal pembelajaran ini? Menghapus jurnal juga akan menghapus semua data presensi siswa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteJournal(id);
            }
        });
    }

    // Hapus jurnal
    function deleteJournal(id) {
        Swal.fire({
            title: 'Menghapus...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("{{ url('admin/teaching-journals') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('teaching-journals.index') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan. Silakan coba lagi.'
                });
            });
    }

    // Hapus materi
   function deleteMaterial(journalId, materialId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus materi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Gunakan URL dengan journalId dan materialId
            const url = '{{ url("admin/teaching-journals") }}/' + journalId + '/materials/' + materialId;
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan. Silakan coba lagi.' });
            });
        }
    });
}

    // Tampilkan daftar siswa penerima materi
    function showStudents(materialId) {
        Swal.fire({
            title: 'Daftar Siswa Penerima Materi',
            html: '<div id="studentListModal"><div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Memuat...</p></div></div>',
            showCloseButton: true,
            showConfirmButton: false,
            width: 600,
            didOpen: () => {
                fetch('{{ url("admin/teaching-journals/materials") }}/' + materialId + '/students')
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data.length > 0) {
                            html = '<ul class="list-group">';
                            data.forEach(student => {
                                html += '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    student.name +
                                    '<span class="badge bg-primary rounded-pill">' + student.nis + '</span>' +
                                    '</li>';
                            });
                            html += '</ul>';
                        } else {
                            html = '<p class="text-muted">Tidak ada siswa yang menerima materi ini.</p>';
                        }
                        document.getElementById('studentListModal').innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('studentListModal').innerHTML = '<p class="text-danger">Gagal memuat data. Silakan coba lagi.</p>';
                    });
            }
        });
    }

    // ========== FUNGSI TOGGLE ==========

    // Toggle tipe materi (file/link)
    function toggleMaterialType() {
        const typeFile = document.getElementById('typeFile');
        const typeLink = document.getElementById('typeLink');
        const fileGroup = document.getElementById('fileInputGroup');
        const linkGroup = document.getElementById('linkInputGroup');
        const fileInput = document.getElementById('file');
        const linkInput = document.getElementById('link_url');

        if (!typeFile || !typeLink || !fileGroup || !linkGroup) return;

        if (typeFile.checked) {
            fileGroup.classList.remove('d-none');
            linkGroup.classList.add('d-none');
            if (fileInput) fileInput.required = true;
            if (linkInput) linkInput.required = false;
        } else if (typeLink.checked) {
            fileGroup.classList.add('d-none');
            linkGroup.classList.remove('d-none');
            if (fileInput) fileInput.required = false;
            if (linkInput) linkInput.required = true;
        }
    }

    // Toggle pilihan siswa (semua / custom)
    function toggleStudentSelection() {
        const optionAll = document.getElementById('allStudents');
        const optionSelected = document.getElementById('selectedStudents');
        const container = document.getElementById('studentSelectionContainer');

        if (!optionAll || !optionSelected || !container) return;

        if (optionSelected.checked) {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
            // Uncheck semua checkbox siswa
            document.querySelectorAll('input[name="student_ids[]"]').forEach(el => el.checked = false);
        }
    }

    // ========== JQUERY READY ==========
    $(document).ready(function() {
        console.log('✅ Detail Jurnal siap digunakan.');

        // Set initial state
        toggleMaterialType();
        toggleStudentSelection();

        // Event listeners untuk radio button
        document.querySelectorAll('input[name="type"]').forEach(el => {
            el.addEventListener('change', toggleMaterialType);
        });

        document.querySelectorAll('input[name="student_option"]').forEach(el => {
            el.addEventListener('change', toggleStudentSelection);
        });

        // File input preview (jQuery)
        $('#file').on('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Tidak ada file dipilih';
            $('label[for="file"]').html('Pilih File <span class="text-danger">*</span> <small class="text-muted">(' + fileName + ')</small>');
        });
    });
</script>
@endpush