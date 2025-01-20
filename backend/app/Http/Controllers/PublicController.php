<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Enums\Announcement\Active;
use App\Enums\Announcement\Messages;
use App\Http\Resources\Announcement\PublicAnnouncementResource;

class PublicController extends Controller
{
    public function announcements()
    {
        $perPage = request('per_page', 10);

        $announcements = Announcement::query()
            ->when(request('sort_by'), function ($query, $sortBy) {
                return $query->orderBy($sortBy, request('sort_direction', 'asc'));
            })
            ->where('active', Active::YES)
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);

        $transformedAnnouncements = PublicAnnouncementResource::collection($announcements);

        return response()->json([
            'message' => Messages::FETCH,
            'data' => $transformedAnnouncements,
            'pagination' => [
                'total' => $announcements->total(),
                'per_page' => $announcements->perPage(),
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'next_page_url' => $announcements->nextPageUrl(),
                'prev_page_url' => $announcements->previousPageUrl(),
            ]
        ], 200);
    }
}
