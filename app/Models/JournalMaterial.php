<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JournalMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_journal_id',
        'title',
        'type',
        'path',
        'url',
        'file_name',
        'file_size',
        'mime_type',
        'is_for_all_students'
    ];

    protected $casts = [
        'is_for_all_students' => 'boolean',
    ];

    public function teachingJournal()
    {
        return $this->belongsTo(TeachingJournal::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'journal_material_students')
            ->withPivot('is_viewed', 'viewed_at')
            ->withTimestamps();
    }

    public function getFileUrlAttribute()
    {
        if ($this->type === 'file' && $this->path) {
            return Storage::url($this->path);
        }
        return null;
    }

    public function getFileIconAttribute()
    {
        if ($this->type === 'link') {
            return 'bi-link-45deg';
        }

        $mimeTypes = [
            'pdf' => 'bi-filetype-pdf',
            'doc' => 'bi-file-word',
            'docx' => 'bi-file-word',
            'xls' => 'bi-file-excel',
            'xlsx' => 'bi-file-excel',
            'ppt' => 'bi-file-ppt',
            'pptx' => 'bi-file-ppt',
            'jpg' => 'bi-file-image',
            'jpeg' => 'bi-file-image',
            'png' => 'bi-file-image',
            'gif' => 'bi-file-image',
            'mp4' => 'bi-file-play',
            'mp3' => 'bi-file-music',
            'zip' => 'bi-file-zip',
            'rar' => 'bi-file-zip',
        ];

        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return $mimeTypes[$extension] ?? 'bi-file-earmark';
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return '-';
        $bytes = (int) $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}
