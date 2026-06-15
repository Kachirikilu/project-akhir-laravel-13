<?php

namespace App\Jobs;

use App\Http\Services\RekapCapaian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessRekapCapaian implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prId;

    protected $cooldown;

    public function __construct($prId = null, $cooldown = 60)
    {
        $this->prId = $prId;
        $this->cooldown = $cooldown;
    }

    public $timeout = 86400;
    public $failOnTimeout = true;

    public function handle()
    {
        try {
            // =========================================================================
            // TRIK ANONYMOUS CLASS: Memakai Trait secara instan tanpa membuat file baru
            // =========================================================================
            $rekapService = new class
            {
                use RekapCapaian;
            };

            // Sekarang method di dalam Trait bisa dipanggil langsung dengan aman!
            $rekapService->generateRekapCapaianQueue($this->prId);

            $cacheKey = 'cooldown_rekap_pr_'.($this->prId ?? 'all');
            $waktuSelesaiCooldown = time() + ($this->cooldown * 60);
            Cache::put($cacheKey, $waktuSelesaiCooldown, now()->addMinutes($this->cooldown));

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
