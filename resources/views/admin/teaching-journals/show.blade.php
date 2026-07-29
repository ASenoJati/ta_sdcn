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
@php
    Log::info('DATA', ['journal' => $journal])
@endphp
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
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-clipboard-check me-2"></i> Ringkasan Presensi
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            $summary = $journal->attendance_summary;
                        @endphp
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="bi bi-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hadir</span>
                                        <span class="info-box-number">{{ $summary['hadir'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="bi bi-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Izin</span>
                                        <span class="info-box-number">{{ $summary['izin'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="bi bi-heart"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sakit</span>
                                        <span class="info-box-number">{{ $summary['sakit'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="bi bi-x-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Alpa</span>
                                        <span class="info-box-number">{{ $summary['alpa'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Total Siswa: {{ $summary['total'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Presensi Siswa -->
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-people me-2"></i> Daftar Presensi Siswa
                        </h3>
                    </div>
                    <div class="card-body">
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
                                    @forelse($journal->attendances as $index => $attendance)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attendance->student->nis ?? '-' }}</td>
                                            <td>{{ $attendance->student->name ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'hadir' => 'success',
                                                        'izin' => 'warning',
                                                        'sakit' => 'info',
                                                        'alpa' => 'danger'
                                                    ];
                                                    $labelMap = [
                                                        'hadir' => 'Hadir',
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
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada data presensi</td>
                                        </tr>
                                    @endforelse
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
    .info-box.bg-success { background: #d4edda; color: #155724; }
    .info-box.bg-warning { background: #fff3cd; color: #856404; }
    .info-box.bg-info { background: #d1ecf1; color: #0c5460; }
    .info-box.bg-danger { background: #f8d7da; color: #721c24; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi konfirmasi hapus dengan SweetAlert2
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

    // Fungsi untuk menghapus jurnal
    function deleteJournal(id) {
        // Tampilkan loading
        Swal.fire({
            title: 'Menghapus...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim request DELETE dengan fetch
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
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan. Silakan coba lagi.'
            });
        });
    }
</script>
@endpush