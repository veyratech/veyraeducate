<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\WebApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::query()
                ->with('webApps')
                ->withCount([
                    'subscriptions as active_subscriptions_count' => fn ($q) => $q
                        ->withoutGlobalScopes()
                        ->where('status', 'active')
                        ->where('ends_at', '>', now()),
                ])
                ->orderBy('sort_order')
                ->get(),
            'webApps' => WebApp::enabled()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.create', [
            'webApps' => WebApp::enabled()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', 'regex:/^[a-z0-9\-]+$/', 'unique:plans,code'],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price_idr' => ['required', 'integer', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            'duration_label' => ['required', 'string', 'max:60'],
            'badge' => ['nullable', 'string', 'max:120'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'web_app_ids' => ['nullable', 'array'],
            'web_app_ids.*' => ['integer', 'exists:web_apps,id'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['features'] = array_values(array_filter(array_map(
            fn ($f) => trim((string) $f),
            $validated['features'] ?? []
        )));

        $plan = Plan::query()->create($validated);

        if ($plan->is_default) {
            Plan::query()->where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        if ($request->has('web_app_ids')) {
            $plan->webApps()->sync($request->input('web_app_ids', []));
        }

        AuditLog::record('plan_created', $plan, [
            'code' => $plan->code,
            'label' => $plan->label,
            'price_idr' => $plan->price_idr,
        ]);

        return redirect()
            ->route('admin.plans.edit', $plan)
            ->with('status', 'Paket berhasil dibuat.');
    }

    public function edit(Plan $plan): View
    {
        $plan->load('webApps');

        return view('admin.plans.edit', [
            'plan' => $plan,
            'webApps' => WebApp::enabled()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price_idr' => ['required', 'integer', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            'duration_label' => ['required', 'string', 'max:60'],
            'badge' => ['nullable', 'string', 'max:120'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'web_app_ids' => ['nullable', 'array'],
            'web_app_ids.*' => ['integer', 'exists:web_apps,id'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['features'] = array_values(array_filter(array_map(
            fn ($f) => trim((string) $f),
            $validated['features'] ?? []
        )));

        $plan->update($validated);

        if ($plan->is_default) {
            Plan::query()->where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        $plan->webApps()->sync($request->input('web_app_ids', []));

        AuditLog::record('plan_updated', $plan, [
            'code' => $plan->code,
            'label' => $plan->label,
            'price_idr' => $plan->price_idr,
        ]);

        return redirect()
            ->route('admin.plans.index')
            ->with('status', 'Paket berhasil diperbarui.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->is_default) {
            return redirect()
                ->route('admin.plans.index')
                ->with('error', 'Paket default tidak dapat dihapus. Silakan jadikan paket lain sebagai default terlebih dahulu.');
        }

        if ($plan->subscriptions()->exists()) {
            return redirect()
                ->route('admin.plans.index')
                ->with('error', "Paket '{$plan->label}' tidak dapat dihapus karena masih digunakan oleh sekolah yang sedang berlangganan. Anda dapat menonaktifkan statusnya (Nonaktif) agar tidak bisa dibeli lagi.");
        }

        if (PaymentOrder::query()->where('plan_id', $plan->id)->exists()) {
            return redirect()
                ->route('admin.plans.index')
                ->with('error', "Paket '{$plan->label}' memiliki riwayat transaksi pembayaran dan tidak dapat dihapus permanen. Silakan nonaktifkan status 'Aktif dijual'.");
        }

        $label = $plan->label;
        $plan->webApps()->detach();
        $plan->delete();

        return redirect()
            ->route('admin.plans.index')
            ->with('status', "Paket '{$label}' berhasil dihapus.");
    }
}
