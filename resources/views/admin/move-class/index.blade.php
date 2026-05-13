@extends('layouts.admin.app')

@section('title', 'Pindah Kelas')

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Pindah Kelas</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pindah Kelas</li>
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
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-arrow-left-right me-2"></i> Pindahkan Siswa ke Kelas Lain
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="moveClassForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-3">
                                        <label for="from_classroom_id" class="form-label">
                                            <i class="bi bi-building"></i> Dari Kelas <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="from_classroom_id" name="from_classroom_id" required>
                                            <option value="">-- Pilih Kelas Asal --</option>
                                            @foreach($classrooms as $classroom)
                                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 text-center">
                                    <div class="mt-4 pt-2">
                                        <i class="bi bi-arrow-right-circle-fill fs-1 text-primary"></i>
                                    </div>
                                </div>
                                
                                <div class="col-md-5">
                                    <div class="form-group mb-3">
                                        <label for="to_classroom_id" class="form-label">
                                            <i class="bi bi-building"></i> Ke Kelas <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="to_classroom_id" name="to_classroom_id" required>
                                            <option value="">-- Pilih Kelas Tujuan --</option>
                                            @foreach($classrooms as $classroom)
                                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <!-- Kolom Kelas Asal -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h4 class="card-title mb-0">
                                                <i class="bi bi-box-arrow-right me-2"></i>
                                                Kelas Asal: <span id="fromClassName">-</span>
                                            </h4>
                                            <div class="card-tools">
                                                <span class="badge bg-light text-dark" id="fromStudentCount">0 Siswa</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="fromStudentsList" class="p-3">
                                                <div class="alert alert-info text-center" id="fromEmptyMessage">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    Pilih kelas asal terlebih dahulu
                                                </div>
                                                <div id="fromStudentsContainer" style="display: none;">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-hover" id="fromStudentsTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="5%">
                                                                        <input type="checkbox" id="selectAllFrom">
                                                                    </th>
                                                                    <th width="5%">No</th>
                                                                    <th width="25%">NIS</th>
                                                                    <th width="50%">Nama Siswa</th>
                                                                    <th width="15%">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="fromStudentsTableBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Kolom Kelas Tujuan -->
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header bg-success text-white">
                                            <h4 class="card-title mb-0">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                                Kelas Tujuan: <span id="toClassName">-</span>
                                            </h4>
                                            <div class="card-tools">
                                                <span class="badge bg-light text-dark" id="toStudentCount">0 Siswa</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="toStudentsList" class="p-3">
                                                <div class="alert alert-info text-center" id="toEmptyMessage">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    Pilih kelas tujuan terlebih dahulu
                                                </div>
                                                <div id="toStudentsContainer" style="display: none;">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-hover" id="toStudentsTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="5%">No</th>
                                                                    <th width="25%">NIS</th>
                                                                    <th width="50%">Nama Siswa</th>
                                                                    <th width="20%">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="toStudentsTableBody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info" id="selectionInfo">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <span id="selectedCount">0</span> siswa dipilih untuk dipindahkan
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg" id="btnMove">
                                        <i class="bi bi-arrow-repeat me-2"></i> Pindahkan Siswa
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-lg" id="btnReset">
                                        <i class="bi bi-arrow-clockwise me-2"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
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
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }
    
    .selected-row {
        background-color: #cfe2ff !important;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .checkbox-cell {
        text-align: center;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-tools .badge {
        font-size: 14px;
        padding: 5px 10px;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let fromStudentsData = [];
    let toStudentsData = [];
    let selectedStudents = new Set();
    let fromDataTable = null;
    let toDataTable = null;
    
    // Load students when from_classroom changes
    $('#from_classroom_id, #to_classroom_id').on('change', function() {
        loadBothClassrooms();
    });
    
    // Load both classrooms data
    function loadBothClassrooms() {
        const fromClassroomId = $('#from_classroom_id').val();
        const toClassroomId = $('#to_classroom_id').val();
        
        if (!fromClassroomId && !toClassroomId) {
            resetAllDisplay();
            return;
        }
        
        // Update class names
        $('#fromClassName').text(fromClassroomId ? $('#from_classroom_id option:selected').text() : '-');
        $('#toClassName').text(toClassroomId ? $('#to_classroom_id option:selected').text() : '-');
        
        // Show loading
        showLoading();
        
        $.ajax({
            url: "{{ route('move-class.get-students') }}",
            type: "GET",
            data: { 
                from_classroom_id: fromClassroomId,
                to_classroom_id: toClassroomId 
            },
            success: function(response) {
                fromStudentsData = response.from_students || [];
                toStudentsData = response.to_students || [];
                
                renderFromStudentsTable();
                renderToStudentsTable();
                
                $('#fromStudentCount').text(fromStudentsData.length + ' Siswa');
                $('#toStudentCount').text(toStudentsData.length + ' Siswa');
            },
            error: function(xhr) {
                console.error('Error loading students:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data siswa.'
                });
            }
        });
    }
    
    function showLoading() {
        $('#fromStudentsTableBody').html('<tr><td colspan="5" class="text-center"><div class="loading-spinner"></div> Memuat data...</td></tr>');
        $('#toStudentsTableBody').html('<tr><td colspan="4" class="text-center"><div class="loading-spinner"></div> Memuat data...</td></tr>');
        $('#fromStudentsContainer').show();
        $('#toStudentsContainer').show();
        $('#fromEmptyMessage').hide();
        $('#toEmptyMessage').hide();
    }
    
    function resetAllDisplay() {
        fromStudentsData = [];
        toStudentsData = [];
        selectedStudents.clear();
        $('#fromStudentsContainer').hide();
        $('#toStudentsContainer').hide();
        $('#fromEmptyMessage').show();
        $('#toEmptyMessage').show();
        $('#fromStudentCount').text('0 Siswa');
        $('#toStudentCount').text('0 Siswa');
        updateSelectionInfo();
    }
    
    // Render from students table (kelas asal)
    function renderFromStudentsTable() {
        if (fromDataTable) {
            fromDataTable.destroy();
        }
        
        const tbody = $('#fromStudentsTableBody');
        tbody.empty();
        
        if (fromStudentsData.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada siswa di kelas ini</td></tr>');
            $('#fromStudentsContainer').show();
            return;
        }
        
        fromStudentsData.forEach((student, index) => {
            const isChecked = selectedStudents.has(student.id);
            const row = `
                <tr class="${isChecked ? 'selected-row' : ''}">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="student-checkbox-from" value="${student.id}" data-name="${student.name}" ${isChecked ? 'checked' : ''}>
                    </td>
                    <td>${index + 1}</td>
                    <td>${student.nis}</td>
                    <td>${student.name}</td>
                    <td><span class="badge bg-warning">Akan Dipindah</span></td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Initialize DataTable for from students
        fromDataTable = $('#fromStudentsTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 siswa",
                zeroRecords: "Tidak ada siswa yang ditemukan",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            pageLength: 10,
            order: [[2, 'asc']],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            destroy: true
        });
        
        // Bind checkbox events
        $('.student-checkbox-from').on('change', function() {
            const studentId = parseInt($(this).val());
            const row = $(this).closest('tr');
            
            if ($(this).is(':checked')) {
                selectedStudents.add(studentId);
                row.addClass('selected-row');
            } else {
                selectedStudents.delete(studentId);
                row.removeClass('selected-row');
            }
            
            updateSelectionInfo();
            updateSelectAllCheckboxes();
        });
        
        // Select all from checkbox
        $('#selectAllFrom').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            const visibleRows = fromDataTable.rows({ search: 'applied' }).nodes();
            
            $(visibleRows).find('.student-checkbox-from').each(function() {
                const studentId = parseInt($(this).val());
                const row = $(this).closest('tr');
                
                if (isChecked) {
                    if (!selectedStudents.has(studentId)) {
                        selectedStudents.add(studentId);
                    }
                    $(this).prop('checked', true);
                    row.addClass('selected-row');
                } else {
                    if (selectedStudents.has(studentId)) {
                        selectedStudents.delete(studentId);
                    }
                    $(this).prop('checked', false);
                    row.removeClass('selected-row');
                }
            });
            
            updateSelectionInfo();
        });
        
        updateSelectionInfo();
        $('#fromStudentsContainer').show();
    }
    
    // Render to students table (kelas tujuan)
    function renderToStudentsTable() {
        if (toDataTable) {
            toDataTable.destroy();
        }
        
        const tbody = $('#toStudentsTableBody');
        tbody.empty();
        
        if (toStudentsData.length === 0) {
            tbody.html('<tr><td colspan="4" class="text-center text-muted">Tidak ada siswa di kelas ini</td></tr>');
            $('#toStudentsContainer').show();
            return;
        }
        
        toStudentsData.forEach((student, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${student.nis}</td>
                    <td>${student.name}</td>
                    <td><span class="badge bg-info">Siswa Aktif</span></td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Initialize DataTable for to students
        toDataTable = $('#toStudentsTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 siswa",
                zeroRecords: "Tidak ada siswa yang ditemukan",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            pageLength: 10,
            order: [[1, 'asc']],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            destroy: true
        });
        
        $('#toStudentsContainer').show();
    }
    
    // Update selection info
    function updateSelectionInfo() {
        const count = selectedStudents.size;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#selectionInfo').removeClass('alert-info').addClass('alert-success');
        } else {
            $('#selectionInfo').removeClass('alert-success').addClass('alert-info');
        }
    }
    
    // Update select all checkboxes
    function updateSelectAllCheckboxes() {
        const totalStudents = fromStudentsData.length;
        const selectedCount = selectedStudents.size;
        
        if (totalStudents > 0 && selectedCount === totalStudents) {
            $('#selectAllFrom').prop('checked', true);
        } else {
            $('#selectAllFrom').prop('checked', false);
        }
    }
    
    // Reset form
    $('#btnReset').on('click', function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua pilihan akan direset!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#moveClassForm')[0].reset();
                $('#from_classroom_id').val('');
                $('#to_classroom_id').val('');
                fromStudentsData = [];
                toStudentsData = [];
                selectedStudents.clear();
                $('#fromStudentsContainer').hide();
                $('#toStudentsContainer').hide();
                $('#fromEmptyMessage').show();
                $('#toEmptyMessage').show();
                $('#fromStudentCount').text('0 Siswa');
                $('#toStudentCount').text('0 Siswa');
                $('#fromClassName').text('-');
                $('#toClassName').text('-');
                updateSelectionInfo();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Reset Berhasil!',
                    text: 'Form telah direset.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
    
    // Submit form
    $('#moveClassForm').on('submit', function(e) {
        e.preventDefault();
        
        const fromClassroomId = $('#from_classroom_id').val();
        const toClassroomId = $('#to_classroom_id').val();
        const studentIds = Array.from(selectedStudents);
        const fromClassName = $('#from_classroom_id option:selected').text();
        const toClassName = $('#to_classroom_id option:selected').text();
        
        if (!fromClassroomId) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Pilih kelas asal terlebih dahulu!'
            });
            return;
        }
        
        if (!toClassroomId) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Pilih kelas tujuan!'
            });
            return;
        }
        
        if (fromClassroomId === toClassroomId) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Kelas asal dan tujuan tidak boleh sama!'
            });
            return;
        }
        
        if (studentIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Pilih minimal satu siswa untuk dipindahkan!'
            });
            return;
        }
        
        Swal.fire({
            title: 'Konfirmasi Pindah Kelas',
            html: `
                <div class="text-start">
                    <p>Apakah Anda yakin ingin memindahkan:</p>
                    <p class="fw-bold text-primary">${studentIds.length} siswa</p>
                    <p>Dari kelas: <strong class="text-danger">${fromClassName}</strong></p>
                    <p>Ke kelas: <strong class="text-success">${toClassName}</strong></p>
                    <hr>
                    <p class="text-warning mt-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Pindahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: "{{ route('move-class.move') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        from_classroom_id: fromClassroomId,
                        to_classroom_id: toClassroomId,
                        student_ids: studentIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reset form and reload
                                $('#moveClassForm')[0].reset();
                                $('#from_classroom_id').val('');
                                $('#to_classroom_id').val('');
                                fromStudentsData = [];
                                toStudentsData = [];
                                selectedStudents.clear();
                                $('#fromStudentsContainer').hide();
                                $('#toStudentsContainer').hide();
                                $('#fromEmptyMessage').show();
                                $('#toEmptyMessage').show();
                                $('#fromStudentCount').text('0 Siswa');
                                $('#toStudentCount').text('0 Siswa');
                                $('#fromClassName').text('-');
                                $('#toClassName').text('-');
                                updateSelectionInfo();
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush