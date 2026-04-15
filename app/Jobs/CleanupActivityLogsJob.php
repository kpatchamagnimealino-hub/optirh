<?php

namespace App\Jobs;

use App\Services\ActivityLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupActivityLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Le nombre de jours de logs à conserver.
     *
     * @var int
     */
    protected $days;

    /**
     * Création d'une nouvelle instance de job.
     *
     * @return void
     */
    public function __construct(int $days = 90)
    {
        $this->days = $days;
    }

    /**
     * Exécuter le job.
     *
     * @return void
     */
    public function handle(ActivityLogService $activityLogService)
    {
        $activityLogService->cleanup($this->days);
    }
}
