<?php

namespace App\Models;

use App\Observers\AnnouncementObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([AnnouncementObserver::class])]
class Announcement extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'content',
        'start_date',
        'end_date',
        'active'
    ];

    protected $auditInclude = [
        'title',
        'content',
        'start_date',
        'end_date',
        'active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
