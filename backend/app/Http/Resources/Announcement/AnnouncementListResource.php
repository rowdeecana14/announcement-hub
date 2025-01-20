<?php

namespace App\Http\Resources\Announcement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AnnouncementListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => Str::limit($this->title, 50),
            'content' => Str::limit($this->content, 60),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'active' => $this->active === 1 ? true : false
        ];
    }
}
