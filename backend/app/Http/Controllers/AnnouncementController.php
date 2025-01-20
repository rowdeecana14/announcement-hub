<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Resources\Announcement\AnnouncementResource;
use App\Http\Resources\Announcement\AnnouncementListResource;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateDatesAnnouncementRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Enums\Announcement\Messages;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'title');
        $sortDirection = $request->input('sort_direction', 'asc');

        $announcements = Announcement::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%");
                });
            })
            ->when(request('sort_by'), function ($query) use ($sortBy, $sortDirection) {
                return $query->orderBy($sortBy, $sortDirection);
            })
            ->paginate($perPage);
        $transformedAnnouncements = AnnouncementListResource::collection($announcements);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request)
    {

        $announcement = Announcement::create($request->validated());

        return response()->json([
            'message' => Messages::CREATED,
            'data' => new AnnouncementResource($announcement)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($announcementId)
    {
        $announcement = Announcement::with(['user' => function (Builder $query) {
            $query->select('id', 'name', 'email');
        }])
            ->find($announcementId);

        if (!$announcement) {
            return response()->json([
                'message' => Messages::NOT_FOUND,
            ], 404);
        }

        return response()->json([
            'message' => Messages::FOUND,
            'data' => new AnnouncementResource($announcement)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, $announcementId)
    {
        $announcement = Announcement::find($announcementId);

        if (!$announcement) {
            return response()->json([
                'message' => Messages::NOT_FOUND,
            ], 404);
        }

        $announcement->update($request->validated());

        return response()->json([
            'message' => Messages::UPDATED,
            'data' => new AnnouncementResource($announcement)
        ], 200);
    }

    public function updateDates(UpdateDatesAnnouncementRequest $request, $announcementId)
    {
        $announcement = Announcement::find($announcementId);

        if (!$announcement) {
            return response()->json([
                'message' => Messages::NOT_FOUND,
            ], 404);
        }

        $announcement->update($request->validated());

        return response()->json([
            'message' => Messages::UPDATED_DATES,
            'data' => new AnnouncementResource($announcement)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($announcementId)
    {
        $announcement = Announcement::find($announcementId);

        if (!$announcement) {
            return response()->json([
                'message' => Messages::NOT_FOUND,
            ], 404);
        }

        $announcement->delete();

        return response()->json([
           'message' => Messages::DELETED,
        ], 200);
    }
}
