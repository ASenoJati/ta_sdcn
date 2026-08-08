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
                    <a href="{{ route('classrooms.index') }}" class="small-box-footer link-light">
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
                    <a href="{{ route('classroom-schedules.index') }}" class="small-box-footer link-light">
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
    </div>
</div>
<!--end::App Content-->
@endsection

@push('styles')
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