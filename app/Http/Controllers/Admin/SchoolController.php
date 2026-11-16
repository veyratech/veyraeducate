<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SchoolLookupService;
use App\SubscriptionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function __construct(
        private readonly SchoolLookupService $lookupService
    ) {}

    public function lookupSekolah(string $code): JsonResponse
    {
        $result = $this->lookupService->lookup(trim($code));

        return response()->json($result);
    }

    public function lookupNpsn(string $npsn): JsonResponse
    {
        return response()->json($this->lookupService->lookup($npsn));
    }

    public function lookupNsm(string $nsm): JsonResponse
    {
        return response()->json($this->lookupService->lookup($nsm));
    }

    public function index(Request $request): View
    {
        $query = School::query()
            ->withCount('users')
            ->with(['owner', 'subscriptions.plan'])
            ->latest('id');

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $escapedSearch = str_replace(['%', '_'], '\\\\$0', $search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', "%{$escapedSearch}%")
                    ->orWhere('npsn', 'like', "%{$escapedSearch}%")
                    ->orWhere('email', 'like', "%{$escapedSearch}%")
                    ->orWhere('city', 'like', "%{$escapedSearch}%");
            });
        }

        return view('admin.schools.index', [
            'schools' => $query->paginate(10)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function show(School $school): View
    {
        $school->load([
            'owner',
            'users',
            'subscriptions.plan',
            'paymentOrders.plan',
        ]);

        return view('admin.schools.show', [
            'school' => $school,
            'subscriptions' => $school->subscriptions()->with('plan')->latest('id')->get(),
            'orders' => $school->paymentOrders()->with('plan')->latest('id')->limit(20)->get(),
            'users' => $school->users()->latest('id')->get(),
            'plans' => Plan::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:16'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
        ]);

        $owner = User::query()->create([
            'name' => $validated['owner_name'],
            'email' => $validated['owner_email'],
            'password' => $validated['owner_password'],
            'role' => User::ROLE_SCHOOL,
            'email_verified_at' => now(),
            'phone' => $validated['phone'] ?? null,
        ]);

        $school = School::query()->create([
            'owner_user_id' => $owner->id,
            'name' => $validated['name'],
            'npsn' => $validated['npsn'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
        ]);

        $owner->forceFill(['school_id' => $school->id])->save();

        AuditLog::record('school_created', $school, [
            'name' => $school->name,
            'npsn' => $school->npsn,
            'owner_email' => $owner->email,
        ]);

        return redirect()
            ->route('admin.schools.show', $school)
            ->with('status', 'Sekolah berhasil ditambahkan.');
    }

    public function edit(School $school): View
    {
        return view('admin.schools.edit', ['school' => $school]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:500'],
            'headmaster' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:16'],
        ]);

        $meta = $school->meta ?? [];
        if (array_key_exists('address', $validated)) {
            $meta['address'] = $validated['address'];
        }
        if (array_key_exists('headmaster', $validated)) {
            $meta['headmaster'] = $validated['headmaster'];
            $meta['headmaster_name'] = $validated['headmaster'];
        }

        $school->update([
            'name' => $validated['name'],
            'npsn' => $validated['npsn'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'meta' => $meta,
        ]);

        AuditLog::record('school_updated', $school, [
            'name' => $school->name,
            'npsn' => $school->npsn,
        ]);

        return redirect()
            ->route('admin.schools.show', $school)
            ->with('status', 'Sekolah berhasil diperbarui.');
    }

    public function grantSubscription(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'string', 'in:trial,plan'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'duration_months' => ['nullable', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $plan = ! empty($validated['plan_id'])
            ? Plan::query()->findOrFail($validated['plan_id'])
            : (Plan::getDefault()
                ?? Plan::query()->where('is_active', true)->orderBy('sort_order')->first()
                ?? Plan::query()->first());

        if (! $plan) {
            return back()->with('error', 'Belum ada paket langganan yang tersedia di sistem.');
        }

        $activeSub = $school->activeSubscription()->first();

        if ($validated['mode'] === 'trial') {
            $days = (int) ($validated['trial_days'] ?? 14);

            if ($activeSub) {
                $baseEnd = $activeSub->ends_at && $activeSub->ends_at->isFuture() ? $activeSub->ends_at->copy() : now();
                $meta = is_array($activeSub->meta) ? $activeSub->meta : [];
                $meta['last_trial_grant_days'] = $days;
                $meta['notes'] = $validated['notes'] ?? ($meta['notes'] ?? null);

                $activeSub->forceFill([
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active,
                    'ends_at' => $baseEnd->addDays($days),
                    'meta' => $meta,
                ])->save();
            } else {
                Subscription::query()->create([
                    'school_id' => $school->id,
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($days),
                    'activated_at' => now(),
                    'meta' => [
                        'type' => 'trial',
                        'trial_days' => $days,
                        'granted_by' => auth()->id(),
                        'notes' => $validated['notes'] ?? null,
                    ],
                ]);
            }

            $school->grantPlanAppAccess($plan);

            AuditLog::record('school_trial_granted', $school, [
                'plan' => $plan->code,
                'trial_days' => $days,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route('admin.schools.show', $school)
                ->with('status', "Free Trial {$days} hari ({$plan->label}) berhasil diaktifkan untuk {$school->name}.");
        }

        $months = (int) ($validated['duration_months'] ?? $plan->duration_months ?? 6);

        if ($activeSub) {
            $baseEnd = $activeSub->ends_at && $activeSub->ends_at->isFuture() ? $activeSub->ends_at->copy() : now();
            $meta = is_array($activeSub->meta) ? $activeSub->meta : [];
            $meta['last_manual_grant_months'] = $months;
            $meta['notes'] = $validated['notes'] ?? ($meta['notes'] ?? null);

            $activeSub->forceFill([
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active,
                'ends_at' => $baseEnd->addMonthsNoOverflow($months),
                'meta' => $meta,
            ])->save();
        } else {
            Subscription::query()->create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Active,
                'starts_at' => now(),
                'ends_at' => now()->addMonthsNoOverflow($months),
                'activated_at' => now(),
                'meta' => [
                    'type' => 'manual_grant',
                    'duration_months' => $months,
                    'granted_by' => auth()->id(),
                    'notes' => $validated['notes'] ?? null,
                ],
            ]);
        }

        $school->grantPlanAppAccess($plan);

        AuditLog::record('school_subscription_granted', $school, [
            'plan' => $plan->code,
            'duration_months' => $months,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.schools.show', $school)
            ->with('status', "Lisensi paket {$plan->label} ({$months} bulan) berhasil diaktifkan untuk {$school->name}.");
    }

    public function destroy(School $school): RedirectResponse
    {
        AuditLog::record('school_deleted', $school, [
            'name' => $school->name,
            'npsn' => $school->npsn,
        ]);

        $school->delete();

        return redirect()
            ->route('admin.schools.index')
            ->with('status', 'Sekolah dihapus.');
    }

    public function import(Request $request): View
    {
        return view('admin.schools.import', [
            'successes' => null,
            'failures' => null,
        ]);
    }

    public function processImport(Request $request): View
    {
        if (!$request->hasFile('csv_file')) {
            return back()->withErrors(['csv_file' => 'File CSV wajib diunggah.']);
        }
        $csvFile = $request->file('csv_file');
        $csvSizeMB = $csvFile->getSize() / 1024 / 1024;
        if ($csvSizeMB > 5) {
            return back()->withErrors(['csv_file' => 'Ukuran file maksimal 5MB.']);
        }
        $csvExt = strtolower(pathinfo($csvFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if ($csvExt !== 'csv') {
            return back()->withErrors(['csv_file' => 'File harus berupa CSV.']);
        }
        if (!$csvFile->isValid()) {
            return back()->withErrors(['csv_file' => 'Gagal memproses file. Coba gunakan file yang lebih kecil.']);
        }

        $file = $csvFile;
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! is_array($header)) {
            return back()->withErrors(['csv_file' => 'File CSV kosong atau tidak valid.']);
        }

        $expected = ['nama_sekolah', 'npsn', 'email', 'kota', 'telepon'];
        $colMap = [];
        foreach ($expected as $col) {
            $idx = array_search($col, array_map('trim', $header), true);
            $colMap[$col] = $idx !== false ? $idx : null;
        }

        $successes = [];
        $failures = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            $nama = $colMap['nama_sekolah'] !== null && isset($row[$colMap['nama_sekolah']]) ? trim((string) $row[$colMap['nama_sekolah']]) : '';
            $npsn = $colMap['npsn'] !== null && isset($row[$colMap['npsn']]) ? trim((string) $row[$colMap['npsn']]) : '';
            $email = $colMap['email'] !== null && isset($row[$colMap['email']]) ? trim((string) $row[$colMap['email']]) : '';
            $kota = $colMap['kota'] !== null && isset($row[$colMap['kota']]) ? trim((string) $row[$colMap['kota']]) : '';
            $telepon = $colMap['telepon'] !== null && isset($row[$colMap['telepon']]) ? trim((string) $row[$colMap['telepon']]) : '';

            if (empty($nama) && empty($email)) {
                $failures[] = ['row' => $rowNum, 'error' => 'Kolom nama_sekolah atau email wajib diisi.'];

                continue;
            }

            if (! empty($npsn) && preg_match('/^\d{8}$/', $npsn)) {
                $lookupRes = $this->lookupService->lookup($npsn);
                if ($lookupRes['found'] ?? false) {
                    $nama = $lookupRes['name'] ?: $nama;
                    $kota = $kota ?: ($lookupRes['city'] ?? null);
                }
            }

            if (empty($email) && ! empty($npsn)) {
                $email = strtolower(str_replace(' ', '.', $nama)).'_'.$npsn.'@contoh.id';
            }

            $tempPassword = Str::random(12);
            $ownerName = trim(($colMap['nama_sekolah'] !== null && isset($row[$colMap['nama_sekolah']]) ? $row[$colMap['nama_sekolah']] : ''))
                            ?: ('Operator '.$nama);

            try {
                $owner = User::query()->create([
                    'name' => $ownerName,
                    'email' => $email,
                    'password' => bcrypt($tempPassword),
                    'role' => User::ROLE_SCHOOL,
                    'email_verified_at' => now(),
                    'phone' => $telepon ?: null,
                ]);

                $school = School::query()->create([
                    'owner_user_id' => $owner->id,
                    'name' => $nama,
                    'npsn' => $npsn ?: null,
                    'email' => $email,
                    'phone' => $telepon ?: null,
                    'city' => $kota ?: null,
                ]);

                $owner->forceFill(['school_id' => $school->id])->save();

                $successes[] = [
                    'nama' => $nama,
                    'email' => $email,
                    'temp_password' => $tempPassword,
                    'npsn' => $npsn,
                ];
            } catch (\Throwable $e) {
                Log::warning('Import row '.$rowNum.' failed: '.$e->getMessage());
                $failures[] = ['row' => $rowNum, 'error' => $e->getMessage()];
            }
        }

        fclose($handle);

        return view('admin.schools.import', [
            'successes' => $successes,
            'failures' => $failures,
        ]);
    }
}
