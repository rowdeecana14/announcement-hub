<?php

namespace App\Observers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Enums\Announcement\Active;

class AnnouncementObserver
{
    public function creating(Announcement $announcement)
    {
        if (Auth::check()) {
            $announcement->user_id = Auth::id();
        }

        if($announcement->active) {
            
            if($announcement->active === Active::YES->value) {

                $announcement->active = $this->markActiveStatus($announcement);
            }
        }
    }

    public function updating(Announcement $announcement)
    {
        if($announcement->active) {

            if($announcement->active === Active::YES->value) {

                $announcement->active = $this->markActiveStatus($announcement);
            }
        }
    }

    private function markActiveStatus(Announcement $announcement)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::parse($announcement->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($announcement->end_date)->format('Y-m-d');

        if ($startDate <= $currentDate && $endDate >= $currentDate) {
            return Active::YES;
        }

        return Active::NO;
    }
}
