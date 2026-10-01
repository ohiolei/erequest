<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityService
{
    public function __construct(protected ?Request $request = null) {}

    public function log(string $action, string $description, ?string $subjectType = null, ?int $subjectId = null, array $properties = []): Activity
    {
        return Activity::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties,
            'ip_address' => $this->request?->ip(),
            'user_agent' => $this->request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
