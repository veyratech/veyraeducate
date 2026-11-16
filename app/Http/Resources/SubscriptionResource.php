<?php

namespace App\Http\Resources;

use App\SubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = SubscriptionStatus::tryFrom($this->status);
        $isActive = $status === SubscriptionStatus::Active && $this->ends_at > now();

        return [
            'id' => $this->id,
            'plan' => $this->whenLoaded('plan', fn () => [
                'id' => $this->plan->id,
                'code' => $this->plan->code,
                'label' => $this->plan->label,
                'price_idr' => $this->plan->price_idr,
                'duration_months' => $this->plan->duration_months,
                'duration_label' => $this->plan->duration_label,
            ]),
            'status' => $this->status,
            'status_label' => $status?->label() ?? $this->status,
            'is_active' => $isActive,
            'starts_at' => $this->starts_at?->toISO8601String(),
            'ends_at' => $this->ends_at?->toISO8601String(),
            'activated_at' => $this->activated_at?->toISO8601String(),
            'days_remaining' => $this->ends_at ? (int) max(0, ceil(($this->ends_at->getTimestamp() - now()->getTimestamp()) / 86400)) : null,
            'created_at' => $this->created_at?->toISO8601String(),
            'updated_at' => $this->updated_at?->toISO8601String(),
        ];
    }
}
