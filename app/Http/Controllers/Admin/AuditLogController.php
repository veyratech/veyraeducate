<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('user')->latest('id');

        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($escaped) {
                $q->where('action', 'like', "%{$escaped}%")
                    ->orWhere('auditable_type', 'like', "%{$escaped}%")
                    ->orWhere('ip_address', 'like', "%{$escaped}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$escaped}%")->orWhere('email', 'like', "%{$escaped}%"));
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'actions' => $actions,
            'currentAction' => $action,
            'currentSearch' => $search,
            'totalLogs' => AuditLog::query()->count(),
        ]);
    }
}
