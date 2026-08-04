<div class="mb-3">
    <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
    <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
        <option value="">-- Pilih Tahun Ajaran --</option>
        @foreach($academicYears as $year)
            <option value="{{ $year->id }}" {{ old('academic_year_id', $selected ?? '') == $year->id ? 'selected' : '' }}>
                {{ $year->name }}
                @if($year->is_active)
                    (Aktif)
                @endif
            </option>
        @endforeach
    </select>
    @error('academic_year_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>