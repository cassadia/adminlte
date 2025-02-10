<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearLogTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus log pada table accurate_log_new';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = 'accurate_log_new'; // Ganti dengan nama tabel log Anda
        $days = 20; // Hapus log yang lebih dari 30 hari

        DB::table($table)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info('Log lebih dari ' . $days . ' hari telah dihapus.');
    }
}
