<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()->latest();

        $search = trim((string) $request->input('q', ''));
        $action = trim((string) $request->input('action', ''));
        $entityType = trim((string) $request->input('entity_type', ''));
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('action', 'like', '%'.$search.'%')
                    ->orWhere('entity_type', 'like', '%'.$search.'%')
                    ->orWhere('ip_address', 'like', '%'.$search.'%')
                    ->orWhere('user_id', 'like', '%'.$search.'%')
                    ->orWhere('entity_id', 'like', '%'.$search.'%');
            });
        }

        if ($action !== '') {
            $query->where('action', $action);
        }

        if ($entityType !== '') {
            $query->where('entity_type', $entityType);
        }

        $logs = $query->paginate($perPage)->withQueryString();
        $actionOptions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');
        $entityTypeOptions = AuditLog::query()->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('super-admin.audit-logs', compact(
            'logs',
            'search',
            'action',
            'entityType',
            'perPage',
            'actionOptions',
            'entityTypeOptions'
        ));
    }
}
