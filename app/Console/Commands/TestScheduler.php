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
            case 'getSession':
                $result = $accurateController->getSession();
                break;
            default:
                // Tangani jika tugas tidak valid
                \Log::channel('scheduler')->error('Tugas yang diminta tidak valid pada: ' . now());
                return;
        }

        // dd($result->getData()->message);
        if ($result) {
            if (count($result) > 0) {
                foreach ($result as $results) {
                    if (isset($results['message'])) {
                        \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan: " . "\n" . $results['message']);
                    } else {
                        \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan: " . "\n" . $results);
                    }
                    if (isset($results['data'])) {
                        \Log::channel('scheduler')->info("Detail data session: " . "\n" . print_r($results['data'], true));
                    }
                }
            } else {
                \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan: " . "\n" . $result);
            }
        } else {
            \Log::channel('scheduler')->error("Terjadi kesalahan saat menjalankan fungsi controller '$task': " . "\n" . $result );
        }
    }
}