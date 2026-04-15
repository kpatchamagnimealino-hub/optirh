<?php

namespace App\Console\Commands;

use App\Models\Recours\Appeal;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateAppealDayCount extends Command
{
    protected $signature = 'appeals:update-day-count';

    protected $description = 'Met à jour le nombre de jours pour les recours en cours';

    public function handle()
    {
        $appeals = Appeal::where('analyse_status', 'EN_COURS')
            ->orWhereHas('suspended', function ($query) {
                $query->where('decision', 'SUSPENDU');
            })
            ->get();

        foreach ($appeals as $appeal) {
            $depositDateTime = Carbon::parse($appeal->deposit_date.' '.$appeal->deposit_hour);
            $appeal->day_count = $depositDateTime->diffInDays(Carbon::now());
            $appeal->save();
        }

        $this->info('Mise à jour des délais terminée.');
    }
}
