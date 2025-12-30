<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public function log(string $action, Model $entity, array $oldValues = [], array $newValues = [], ?int $actorId = null): void
    {
        ActivityLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'old_values' => Arr::wrap($oldValues),
            'new_values' => Arr::wrap($newValues),
            'ip' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 255),
            'created_at' => now(),
        ]);
    }
}
