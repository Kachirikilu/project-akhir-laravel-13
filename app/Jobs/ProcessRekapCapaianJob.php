<?php

namespace App\Jobs;

use App\Http\Services\RekapCapaianService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessRekapCapaianJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prId;

    protected $cooldown;

    public function __construct($prId = null, $cooldown = null)
    {
        $this->prId = $prId;

        $defaultCooldown = $prId === null
            ? env('COOLDOWN_REKAP_ALL', 60)
            : env('COOLDOWN_REKAP_PRODI', 15);

        $this->cooldown = (int) ($cooldown ?? $defaultCooldown);
    }

    public $timeout = 86400;

    public $failOnTimeout = true;

    public function handle()
    {
        try {
            $rekapService = new class
            {
                use RekapCapaianService;
            };

            $rekapService->rekapCapaianQueue($this->prId);

            $cacheKey = 'cooldown_rekap_pr_'.($this->prId ?? 'all');
            $waktuSelesaiCooldown = time() + ($this->cooldown * 60);

            Cache::put($cacheKey, $waktuSelesaiCooldown, now()->addMinutes((int) $this->cooldown));

        } finally {
            $runningAllKey = 'rekap_capaian_running_all';
            $runningProdiKey = 'rekap_capaian_running_prodi_ids';

            if ($this->prId === null) {
                Cache::forget($runningAllKey);
            } else {
                $runningProdiIds = Cache::get($runningProdiKey, []);
                $runningProdiIds = array_diff($runningProdiIds, [(int) $this->prId]);

                if (empty($runningProdiIds)) {
                    Cache::forget($runningProdiKey);
                } else {
                    Cache::put($runningProdiKey, array_values($runningProdiIds), now()->addHours(2));
                }
            }
        }
    }
}
