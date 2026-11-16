<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Scope payment orders to the user's school (or user_id if no school is attached yet).
     *
     * @return Builder<PaymentOrder>
     */
    private function scopedOrdersForUser(User $user): Builder
    {
        $schoolId = $user->school_id ?? School::where('owner_user_id', $user->id)->value('id');

        $query = PaymentOrder::withoutGlobalScope(SchoolScope::class);

        if ($schoolId) {
            return $query->where(function (Builder $q) use ($schoolId, $user) {
                $q->where('school_id', $schoolId)
                    ->orWhere('user_id', $user->id);
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function history(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->canAccessBilling()) {
            return redirect()->route('dashboard')->with('error', 'Menu Tagihan & Pembayaran khusus untuk Kepala Sekolah dan Bendahara BOS.');
        }

        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();

        $query = $this->scopedOrdersForUser($user)
            ->with(['plan', 'school', 'subscription'])
            ->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && in_array($statusFilter, ['paid', 'pending', 'failed', 'expired'], true)) {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(10)->withQueryString();

        $totalSpent = $this->scopedOrdersForUser($user)
            ->where('status', PaymentStatus::Paid)
            ->sum('amount_idr');

        $paidCount = $this->scopedOrdersForUser($user)
            ->where('status', PaymentStatus::Paid)
            ->count();

        $pendingCount = $this->scopedOrdersForUser($user)
            ->where('status', PaymentStatus::Pending)
            ->count();

        $activeSubscription = $school?->activeSubscription()->with('plan')->first();
        $plans = Plan::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('pembayaran.history', [
            'orders' => $orders,
            'totalSpent' => $totalSpent,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'activeSubscription' => $activeSubscription,
            'school' => $school,
            'plans' => $plans,
            'currentPlan' => $activeSubscription?->plan,
            'currentStatus' => $statusFilter,
        ]);
    }

    public function show(Request $request, string $orderId): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->canAccessBilling()) {
            return redirect()->route('dashboard')->with('error', 'Menu Tagihan & Pembayaran khusus untuk Kepala Sekolah dan Bendahara BOS.');
        }

        $order = $this->scopedOrdersForUser($user)
            ->where('order_id', $orderId)
            ->with(['plan', 'school', 'subscription'])
            ->firstOrFail();

        return view('pembayaran.show', [
            'order' => $order,
            'plans' => Plan::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'currentPlan' => $user->school?->activeSubscription()->first()?->plan,
        ]);
    }

    public function uploadProof(Request $request, string $orderId): RedirectResponse
    {
        $user = $request->user();

        if (! $user->canAccessBilling()) {
            abort(403, 'Hanya Kepala Sekolah atau Bendahara BOS yang dapat mengunggah bukti pembayaran.');
        }

        if (!$request->hasFile('proof')) {
            return back()->withErrors(['proof' => 'Silakan pilih berkas bukti transfer.']);
        }
        $proofFile = $request->file('proof');
        $proofSizeMB = $proofFile->getSize() / 1024 / 1024;
        if ($proofSizeMB > 5) {
            return back()->withErrors(['proof' => 'Ukuran berkas maksimal 5MB.']);
        }
        $proofExt = strtolower(pathinfo($proofFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if (!in_array($proofExt, ['jpg', 'jpeg', 'png', 'webp', 'pdf'], true)) {
            return back()->withErrors(['proof' => 'Format bukti transfer harus JPG, PNG, WEBP, atau PDF.']);
        }
        if (!$proofFile->isValid()) {
            return back()->withErrors(['proof' => 'Gagal memproses berkas. Coba gunakan file yang lebih kecil.']);
        }

        $order = $this->scopedOrdersForUser($user)
            ->where('order_id', $orderId)
            ->firstOrFail();

        if ($order->isPaid()) {
            return back()->with('status', 'Order ini sudah berstatus lunas.');
        }

        if ($order->proof_path && Storage::disk('public')->exists($order->proof_path)) {
            Storage::disk('public')->delete($order->proof_path);
        }

        $path = $request->file('proof')->store('payment_proofs', 'public');

        $order->forceFill([
            'proof_path' => $path,
            'bank_sender' => $request->input('bank_sender'),
            'notes' => $request->input('notes') ?: $order->notes,
            'payment_method' => 'manual_transfer',
            'status' => PaymentStatus::Pending,
        ])->save();

        return back()->with('status', 'Bukti transfer berhasil diunggah! Tim admin akan segera memverifikasi pembayaran Anda.');
    }

    public function invoice(Request $request, string $orderId): View
    {
        $user = $request->user();
        $query = $user->isAdmin()
            ? PaymentOrder::withoutGlobalScopes()
            : $this->scopedOrdersForUser($user);

        $order = $query
            ->where('order_id', $orderId)
            ->with(['plan', 'school', 'user', 'subscription'])
            ->firstOrFail();

        return view('pembayaran.invoice', [
            'order' => $order,
        ]);
    }

    public function bast(Request $request, string $orderId): View
    {
        $user = $request->user();
        $query = $user->isAdmin()
            ? PaymentOrder::withoutGlobalScopes()
            : $this->scopedOrdersForUser($user);

        $order = $query
            ->where('order_id', $orderId)
            ->with(['plan', 'school', 'user', 'subscription'])
            ->firstOrFail();

        return view('pembayaran.bast', [
            'order' => $order,
        ]);
    }
}
