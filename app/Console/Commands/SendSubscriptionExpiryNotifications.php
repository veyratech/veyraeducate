<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiryNotification;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendSubscriptionExpiryNotifications extends Command
{
    protected $signature = 'subscriptions:expiry-check {--dry-run : Only check without sending notifications} {--force : Send reminders regardless of prior meta tracking}';

    protected $description = 'Send expiry reminder notifications (Database, Email & WhatsApp) for subscriptions ending in 7 or 3 days';

    public function handle(WhatsAppService $whatsApp): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $isForce = (bool) $this->option('force');

        $subscriptions = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->with(['school.owner', 'plan'])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($subscriptions as $subscription) {
            $daysUntilExpiry = (int) round($subscription->ends_at->diffInDays(now(), true));
            $meta = $subscription->meta ?? [];

            if ($daysUntilExpiry === 7 && ($isForce || ! isset($meta['reminded_7days']))) {
                $owner = $subscription->school?->owner;
                if ($owner && $owner->email) {
                    if (! $isDryRun) {
                        Notification::send($owner, new SubscriptionExpiryNotification($subscription));
                        try {
                            $whatsApp->sendExpiryReminder($subscription, 7);
                        } catch (\Throwable $e) {
                            Log::warning('WhatsApp expiry reminder (7 days) failed: '.$e->getMessage());
                        }
                        $subscription->update(['meta' => array_merge($meta, ['reminded_7days' => now()->toISOString()])]);
                    }
                    $sent++;
                }
            }

            if ($daysUntilExpiry === 3 && ($isForce || ! isset($meta['reminded_3days']))) {
                $owner = $subscription->school?->owner;
                if ($owner && $owner->email) {
                    if (! $isDryRun) {
                        Notification::send($owner, new SubscriptionExpiryNotification($subscription));
                        try {
                            $whatsApp->sendExpiryReminder($subscription, 3);
                        } catch (\Throwable $e) {
                            Log::warning('WhatsApp expiry reminder (3 days) failed: '.$e->getMessage());
                        }
                        $subscription->update(['meta' => array_merge($meta, ['reminded_3days' => now()->toISOString()])]);
                    }
                    $sent++;
                }
            }

            $skipped++;
        }

        $this->info("Checked {$skipped} subscriptions. Sent {$sent} notification(s).".($isDryRun ? ' [DRY RUN]' : ''));

        return Command::SUCCESS;
    }
}
