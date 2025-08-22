<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pbs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ClearPbData extends Command
{
    protected $signature = 'pb:clear {--force : Force delete without confirmation}';
    protected $description = 'Clear all PB data and related files';

    public function handle()
    {
        $this->info('🔥 CLEAR PB DATA COMMAND 🔥');
        $this->info('================================');

        // Konfirmasi jika tidak menggunakan --force
        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Tindakan ini akan menghapus SEMUA data PB!');
            $this->warn('   - Semua record PB akan dihapus');
            $this->warn('   - Semua file upload akan dihapus');
            $this->warn('   - Auto increment akan direset ke 1');
            $this->newLine();

            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('❌ Operasi dibatalkan.');
                return 0;
            }
        }

        try {
            $this->info('🚀 Memulai proses penghapusan...');
            $this->newLine();

            // Step 1: Hitung data yang akan dihapus
            $totalPb = Pbs::count();
            $this->info("📊 Total PB yang ditemukan: {$totalPb}");

            if ($totalPb == 0) {
                $this->info('✅ Tidak ada data PB untuk dihapus.');
                return 0;
            }

            // Step 2: Hapus file-file terkait
            $this->info('🗂️  Menghapus file-file terkait...');
            $pbsWithFiles = Pbs::whereNotNull('file_path')->get();
            $deletedFiles = 0;
            $failedFiles = 0;

            foreach ($pbsWithFiles as $pb) {
                if ($pb->file_path) {
                    try {
                        if (Storage::disk('public')->exists($pb->file_path)) {
                            Storage::disk('public')->delete($pb->file_path);
                            $deletedFiles++;
                            $this->line("   ✓ Dihapus: {$pb->file_path}");
                        }
                    } catch (\Exception $e) {
                        $failedFiles++;
                        $this->error("   ✗ Gagal menghapus: {$pb->file_path} - {$e->getMessage()}");
                    }
                }
            }

            // Step 3: Hapus data dari database
            $this->info('🗃️  Menghapus data dari database...');

            // Disable foreign key checks untuk memastikan truncate berhasil
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Truncate table untuk menghapus semua data dan reset auto increment
            Pbs::truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Step 4: Hapus direktori upload PB jika kosong
            $pbUploadDir = 'pb_files';
            if (Storage::disk('public')->exists($pbUploadDir)) {
                $remainingFiles = Storage::disk('public')->files($pbUploadDir);
                if (empty($remainingFiles)) {
                    Storage::disk('public')->deleteDirectory($pbUploadDir);
                    $this->info("🗂️  Direktori kosong dihapus: {$pbUploadDir}");
                }
            }

            // Step 5: Tampilkan ringkasan
            $this->newLine();
            $this->info('✅ PROSES SELESAI!');
            $this->info('==================');
            $this->table(
                ['Item', 'Jumlah', 'Status'],
                [
                    ['Data PB', $totalPb, '✅ Dihapus'],
                    ['File berhasil dihapus', $deletedFiles, $deletedFiles > 0 ? '✅ Dihapus' : '➖ Tidak ada'],
                    ['File gagal dihapus', $failedFiles, $failedFiles > 0 ? '⚠️ Error' : '✅ OK'],
                    ['Auto Increment', '1', '✅ Reset']
                ]
            );

            $this->newLine();
            $this->info('🎉 Database PB sudah kosong dan siap untuk data baru!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Terjadi kesalahan: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
