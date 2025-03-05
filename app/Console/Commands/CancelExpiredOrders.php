<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan pesanan yang sudah lebih dari 24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Order::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->update(['status' => 'cancelled']);

        $this->info("Pesanan yang dibatalkan: " . $expiredOrders);
    }

    public function schedule(): array
    {
        Log::info("Scheduler CancelExpiredOrders dijalankan.");

        return [
            $this->everyMinute(), // Coba setiap menit untuk memastikan bekerja
        ];
    }
}
