<?php

namespace App\Http\Controllers;

use App\Enums\AppRequestCategory;
use App\Enums\AppRequestPriority;
use App\Models\AppRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppRequestController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $school = auth()->user()->school;

        if (! $school) {
            return redirect()->route('dashboard')
                ->with('status', 'Anda belum memiliki sekolah yang terdaftar. Hubungi admin.');
        }

        $requests = AppRequest::query()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingCount = AppRequest::query()
            ->where('status', 'pending')
            ->count();

        return view('school-app.requests.index', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
            'categories' => AppRequestCategory::cases(),
            'priorities' => AppRequestPriority::cases(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $school = auth()->user()->school;

        if (! $school) {
            return redirect()->route('dashboard')
                ->with('status', 'Anda belum memiliki sekolah yang terdaftar.');
        }

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'category' => ['required', 'string', 'in:akademik,keuangan,perpustakaan,kesiswaan,kepegawaian,ekskul,lainnya'],
            'priority' => ['required', 'string', 'in:normal,tinggi,mendesak'],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        AppRequest::query()->create([
            'school_id' => $school->id,
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'target_date' => $validated['target_date'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('school-app.requests.index')
            ->with('status', 'Permintaan aplikasi custom berhasil dikirim. Tim kami akan menindaklanjuti.');
    }

    public function destroy(AppRequest $appRequest): RedirectResponse
    {
        $school = auth()->user()?->school;

        if (! $school || (int) $appRequest->school_id !== (int) $school->id) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan permintaan ini.');
        }

        if ($appRequest->status !== 'pending') {
            return redirect()
                ->route('school-app.requests.index')
                ->with('error', 'Permintaan yang sudah diproses tidak dapat dibatalkan.');
        }

        $appRequest->delete();

        return redirect()
            ->route('school-app.requests.index')
            ->with('status', 'Permintaan aplikasi berhasil dibatalkan.');
    }
}
