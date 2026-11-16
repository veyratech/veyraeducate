<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PaymentOrder;
use App\Models\Subscription;
use App\Services\WhatsAppService;
use App\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $search = trim((string) $request->query('q', ''));

        $query = PaymentOrder::query()->with(['school', 'plan', 'user'])->latest('id');

        if ($status === 'proof') {
            $query->where('status', PaymentStatus::Pending)->whereNotNull('proof_path');
        } elseif ($status !== '' && in_array($status, array_column(PaymentStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $escaped = str_replace(['%', '_'], '\\\\$0', $search);
            $query->where(function ($q) use ($escaped) {
                $q->where('order_id', 'like', "%{$escaped}%")
                    ->orWhereHas('school', fn ($sq) => $sq->where('name', 'like', "%{$escaped}%"))
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$escaped}%"));
            });
        }

        return view('admin.payments.index', [
            'orders' => $query->paginate(12)->withQueryString(),
            'status' => $status,
            'search' => $search,
            'statusCounts' => [
                'all' => PaymentOrder::query()->count(),
                'proof' => PaymentOrder::query()->where('status', PaymentStatus::Pending)->whereNotNull('proof_path')->count(),
                'pending' => PaymentOrder::query()->where('status', PaymentStatus::Pending)->count(),
                'paid' => PaymentOrder::query()->where('status', PaymentStatus::Paid)->count(),
                'failed' => PaymentOrder::query()->where('status', PaymentStatus::Failed)->count(),
                'expired' => PaymentOrder::query()->where('status', PaymentStatus::Expired)->count(),
            ],
        ]);
    }

    public function show(PaymentOrder $order): View
    {
        $order->load(['school', 'plan', 'user', 'subscription.plan']);

        return view('admin.payments.show', [
            'order' => $order,
        ]);
    }

    public function markPaid(PaymentOrder $order): RedirectResponse
    {
        if ($order->status === PaymentStatus::Paid) {
            return back()->with('status', 'Order sudah lunas.');
        }

        $order->forceFill([
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ])->save();

        AuditLog::record('payment_marked_paid', $order, [
            'status' => 'paid',
            'amount' => $order->amount_idr,
            'school' => $order->school?->name,
            'plan' => $order->plan?->label,
        ]);

        $subscription = $order->subscription;
        $months = $order->plan?->duration_months ?? 6;

        if ($subscription) {
            if ($subscription->isActive() && $subscription->ends_at) {
                $subscription->forceFill([
                    'plan_id' => $order->plan_id ?? $subscription->plan_id,
                    'ends_at' => $subscription->ends_at->copy()->addMonthsNoOverflow($months),
                ])->save();
            } else {
                $subscription->activate($months);
            }
        } elseif ($order->school_id && $order->plan_id) {
            $newSub = Subscription::query()->create([
                'school_id' => $order->school_id,
                'plan_id' => $order->plan_id,
                'status' => SubscriptionStatus::Active,
                'starts_at' => now(),
                'ends_at' => now()->addMonthsNoOverflow($months),
                'activated_at' => now(),
            ]);
            $order->forceFill(['subscription_id' => $newSub->id])->save();
        }

        $school = $order->school;
        if ($school) {
            if ($order->plan) {
                $school->grantPlanAppAccess($order->plan);
            } else {
                $school->grantAllAppAccess();
            }
        }

        try {
            app(WhatsAppService::class)->sendPaymentSuccess($order);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp payment notification failed: '.$e->getMessage());
        }

        return back()->with('status', 'Order ditandai lunas & langganan diaktifkan.');
    }

    public function export(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;

        $query = PaymentOrder::query()
            ->with(['school', 'plan', 'user'])
            ->orderBy('id');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $orders = $query->get();

        $response = new StreamedResponse(function () use ($orders) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($fh, ['Tanggal', 'Order ID', 'Sekolah', 'Paket', 'Jumlah (IDR)', 'Status', 'Dibayar Oleh']);
            foreach ($orders as $order) {
                fputcsv($fh, [
                    $order->created_at?->format('Y-m-d H:i:s') ?? '',
                    $order->order_id ?? '',
                    $order->school?->name ?? '',
                    $order->plan?->label ?? '',
                    $order->amount_idr ?? 0,
                    $order->status?->value ?? '',
                    $order->user?->email ?? '',
                ]);
            }
            fclose($fh);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="pembayaran_'.date('Y-m-d').'.csv"',
            'Cache-Control' => 'must-revalidate',
        ]);

        return $response;
    }
}
