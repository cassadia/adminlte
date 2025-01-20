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
            case 'getListItemNew':
                $result = $accurateController->getListItemNew();
                break;
            case 'postTransaction':
                $result = $accurateController->postTransaction();
                break;
            case 'refreshToken':
                $result = $accurateController->refreshToken();
                break;
            case 'updatePriceAndStockNew':
                $result = $accurateController->updatePriceAndStockNew();
                break;
            case 'getSession':
                $result = $accurateController->getSession();
                break;
            default:
                // Tangani jika tugas tidak valid
                \Log::channel('scheduler')->error('Tugas yang diminta tidak valid pada: ' . now());
                return;
        }

        \Log::channel('scheduler')->info('Debug Result:', ['result' => $result]); // Log hasil
        $this->line('Result: ' . json_encode($result)); // Tampilkan ke terminal
    
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            // Akses data dari JsonResponse
            $data = $result->getData(true);
    
            if (isset($data['messages']) && is_array($data['messages']) && count($data['messages']) > 0) {
                foreach ($data['messages'] as $message) {
                    \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan: " . $message['message']);
                }
            } else {
                \Log::channel('scheduler')->info("Fungsi controller '$task' berhasil dijalankan tanpa pesan.");
            }
        } else {
            \Log::channel('scheduler')->error("Terjadi kesalahan: Hasil bukan instance JsonResponse.");
        }        
    }
}