<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->subscription;
        $school = $subscription->school;
        $plan = $subscription->plan;
        $daysUntilExpiry = (int) round(now()->diffInDays($subscription->ends_at, true));
        $subject = $daysUntilExpiry <= 3 ? 'Peringatan: Langganan akan berakhir dalam 3 hari' : 'Langganan akan berakhir dalam 7 hari';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Halo '.$school->name)
            ->line('Langganan Veyra Educate untuk '.($school->name ?? 'sekolah Anda'))
            ->line('Akan berakhir pada '.$subscription->ends_at->locale('id')->isoFormat('dddd, DD MMMM Y'))
            ->line('Paket saat ini: '.$plan?->label ?? '—')
            ->action('Perpanjang Sekarang', url('/dashboard'))
            ->line('Jika Anda sudah memperpanjang, abaikan pesan ini.')
            ->salutation('Tim Veyra Educate');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
