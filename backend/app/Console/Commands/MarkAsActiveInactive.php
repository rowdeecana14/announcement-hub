<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Enums\Announcement\Active;

class MarkAsActiveInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-as-active-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark announcement as active or inactive based on start and end dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        Announcement::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->get()
            ->map(function ($announcement) {
                $announcement->active = Active::YES;
                $announcement->save();
            });

        Announcement::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<', $currentDate)
            ->where('end_date', '<', $currentDate)
            ->get()
            ->map(function ($announcement) {
                $announcement->active = Active::NO;
                $announcement->save();
            });

        $this->info('Entries have been marked as active or inactive successfully!');
    }
}
