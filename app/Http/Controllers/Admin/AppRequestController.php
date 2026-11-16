<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppRequest;
use App\Models\AuditLog;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppRequestController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $statusFilter = trim((string) $request->query('status', ''));

        $counts = [
            'all' => AppRequest::query()->count(),
            'pending' => AppRequest::query()->where('status', 'pending')->count(),
            'approved' => AppRequest::query()->where('status', 'approved')->count(),
            'rejected' => AppRequest::query()->where('status', 'rejected')->count(),
        ];

        $query = AppRequest::query()
            ->with(['school', 'user'])
            ->latest();

        if (in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($escaped) {
                $q->where('name', 'like', "%{$escaped}%")
                    ->orWhere('description', 'like', "%{$escaped}%")
                    ->orWhere('category', 'like', "%{$escaped}%")
                    ->orWhereHas('school', fn ($s) => $s->where('name', 'like', "%{$escaped}%"));
            });
        }

        $recentRequests = $query->limit(50)->get();

        return view('admin.app-requests.index', [
            'pendingCount' => $counts['pending'],
            'counts' => $counts,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'recentRequests' => $recentRequests,
            'schools' => School::query()->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, AppRequest $appRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payload = [
            'status' => $validated['status'],
        ];

        if ($request->has('admin_notes')) {
            $payload['admin_notes'] = $validated['admin_notes'] ?: null;
        }

        $appRequest->update($payload);

        AuditLog::record('app_request_status_updated', $appRequest, [
            'name' => $appRequest->name,
            'status' => $validated['status'],
            'admin_notes' => $payload['admin_notes'] ?? $appRequest->admin_notes,
        ]);

        $statusLabel = match ($validated['status']) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu',
        };

        return redirect()
            ->route('admin.app-requests.index')
            ->with('status', "Permintaan \"{$appRequest->name}\" diperbarui menjadi {$statusLabel}.");
    }
}
