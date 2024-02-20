<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AccurateController;

class TestScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:command {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = $this->argument('task');
        print_r($task);
        $accurateController = new AccurateController();

        switch ($task) {
            case 'getListItem':
                $result = $accurateController->getListItem();
                break;
            case 'postTransaction':
                $result = $accurateController->postTransaction();
                break;
            case 'refreshToken':
                $result = $accurateController->refreshToken();
                break;
            case 'updatePriceAndStock':
                $result = $accurateController->updatePriceAndStock();
                break;
            default:
                // Tangani jika tugas tidak valid
                \Log::channel('scheduler')->error('Tugas yang diminta tidak valid pada: ' . now());
                return;
        }

        if ($result) {
            \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan: " . "\n" . $result);
        } else {
            \Log::channel('scheduler')->error("Terjadi kesalahan saat menjalankan fungsi controller '$task': " . "\n" . $result );
        }
    }
}