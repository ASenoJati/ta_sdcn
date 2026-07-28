<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertDateFormat extends Command
{
    protected $signature = 'date:convert';
    protected $description = 'Mengubah semua format tanggal di seluruh project ke format Indonesia';

    public function handle()
    {
        $this->info('Memulai konversi format tanggal...');

        // Daftar direktori yang akan diproses (view dan controller)
        $directories = [
            resource_path('views'),
            app_path('Http/Controllers'),
        ];

        $patterns = [
            // Pola 1: {{ $variable->created_at }} atau {{ $variable->updated_at }}
            '/\{\{\s*(\$[a-zA-Z0-9_\-]>?[a-zA-Z0-9_\-]*?)\s*\}\}/' => '@date($1)',

            // Pola 2: {{ $variable->created_at->format('d/m/Y') }} atau sejenis
            '/\{\{\s*(\$[a-zA-Z0-9_\-]>?[a-zA-Z0-9_\-]*?)\s*->\s*format\s*\([^\)]*\)\s*\}\}/' => '@date($1)',

            // Pola 3: {{ $variable->created_at->diffForHumans() }} (ubah ke date biasa? opsional)
            // Kita biarkan saja, tidak diganti.

            // Pola 4: @php echo $variable->created_at; @endphp
            '/@php\s*echo\s*(\$[a-zA-Z0-9_\-]>?[a-zA-Z0-9_\-]*?)\s*;?\s*@endphp/' => '@date($1)',
        ];

        $count = 0;

        foreach ($directories as $dir) {
            if (!File::isDirectory($dir)) {
                $this->warn("Direktori $dir tidak ditemukan, dilewati.");
                continue;
            }

            $files = File::allFiles($dir);

            foreach ($files as $file) {
                $path = $file->getPathname();
                $ext = $file->getExtension();

                // Hanya proses file .blade.php dan .php
                if (!in_array($ext, ['php', 'blade.php'])) {
                    continue;
                }

                $content = File::get($path);
                $newContent = $content;

                foreach ($patterns as $pattern => $replacement) {
                    $newContent = preg_replace_callback($pattern, function ($matches) use ($replacement) {
                        // Pastikan matches[1] adalah nama variabel
                        $var = $matches[1];
                        // Jika variabel mengandung '->' kita ganti dengan direktif yang sesuai
                        // Karena kita pakai @date, kita cukup kirim variabel utuh
                        return str_replace('$1', $var, $replacement);
                    }, $newContent);
                }

                if ($newContent !== $content) {
                    File::put($path, $newContent);
                    $this->line("Diperbarui: $path");
                    $count++;
                }
            }
        }

        $this->info("Selesai! $count file telah diperbarui.");
        $this->warn('Pastikan Anda telah membuat helper DateHelper dan mendaftarkan direktif Blade sebelum menjalankan perintah ini.');
        $this->warn('Jika ada format khusus yang tidak terganti, Anda perlu menyesuaikan script.');
    }
}
