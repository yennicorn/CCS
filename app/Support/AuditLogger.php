<?php

namespace App\Support;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log(string $action, string $entityType, ?int $entityId = null, array $context = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'context' => $context,
            'ip_address' => request()->ip(),
        ]);
    }
}
