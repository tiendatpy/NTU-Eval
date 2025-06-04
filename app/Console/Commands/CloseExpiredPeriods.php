<?php

namespace App\Console\Commands;

use App\Models\Periods;
use App\Models\MetaType;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class CloseExpiredPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'periods:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động đóng các đợt đánh giá đã hết hạn';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Lấy ID của trạng thái "Mở" và "Đóng"
        $openStatusId = MetaType::where('category', 'period_status')
            ->where('name', 'Mở')
            ->value('id');
            
        $closedStatusId = MetaType::where('category', 'period_status')
            ->where('name', 'Đóng')
            ->value('id');
            
        if (!$openStatusId || !$closedStatusId) {
            $this->error('Không tìm thấy trạng thái Mở/Đóng trong hệ thống!');
            return 1;
        }
        
        // Tìm các đợt đánh giá đang mở nhưng đã hết hạn
        $expiredPeriods = Periods::where('status_id', $openStatusId)
            ->where('end_date', '<', $now)
            ->get();
            
        $count = 0;
        foreach ($expiredPeriods as $period) {
            $period->status_id = $closedStatusId;
            $period->save();
            $count++;
            
            $this->info("Đã đóng đợt đánh giá: {$period->name} (năm {$period->year})");
        }
        
        if ($count > 0) {
            $this->info("Đã đóng tổng cộng {$count} đợt đánh giá hết hạn.");
        } else {
            $this->info("Không có đợt đánh giá nào cần đóng.");
        }

        Log::info('Running close expired periods command at ' . $now);
        Log::info("Found {$expiredPeriods->count()} expired periods to close");
        Log::info("Closed {$count} expired periods");
        
        return 0;
    }
}
