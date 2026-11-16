<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->verifyNotificationSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = PaymentOrder::query()
            ->where('order_id', $payload['order_id'] ?? '')
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Idempoten: tidak memproses ulang order yang sudah lunas.
        if ($order->status === PaymentStatus::Paid) {
            return response()->json(['status' => 'already_paid']);
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? '');

        $order->forceFill([
            'webhook_payload' => $payload,
            'transaction_id' => $payload['transaction_id'] ?? $order->transaction_id,
            'payment_method' => $paymentType !== '' ? $paymentType : $order->payment_method,
        ]);

        match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept',
            $transactionStatus === 'settlement' => $this->markPaid($order),

            in_array($transactionStatus, ['deny', 'cancel', 'failure'], true),
            $transactionStatus === 'expire' => $this->markFailed($order, $transactionStatus),

            default => null,
        };

        $order->save();

        return response()->json(['status' => 'ok']);
    }

    private function markPaid(PaymentOrder $order): void
    {
        $order->status = PaymentStatus::Paid;
        $order->paid_at = now();
        $order->save();

        $subscription = $order->subscription;

        if ($subscription && ! $subscription->isActive()) {
            $subscription->activate($order->plan?->duration_months);
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
    }

    private function markFailed(PaymentOrder $order, string $transactionStatus): void
    {
        $order->status = $transactionStatus === 'expire'
            ? PaymentStatus::Expired
            : PaymentStatus::Failed;
        $order->save();
    }
}
