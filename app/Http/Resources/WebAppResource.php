<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebAppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'icon' => $this->icon,
            'access_url' => $this->access_url,
            'link' => $this->accessLink(),
            'version' => $this->version,
            'enabled' => $this->enabled,
            'sort_order' => $this->sort_order,
            'file_count' => $this->file_count,
            'plans' => $this->whenLoaded('plans', fn () => $this->plans->map(fn ($p) => [
                'id' => $p->id,
                'code' => $p->code,
                'label' => $p->label,
            ])->values()),
            'created_at' => $this->created_at?->toISO8601String(),
            'updated_at' => $this->updated_at?->toISO8601String(),
        ];
    }
}
