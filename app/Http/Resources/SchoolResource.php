<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
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
            'name' => $this->name,
            'npsn' => $this->npsn,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'district' => $this->district,
            'postal_code' => $this->postal_code,
            'logo_url' => $this->logo_path ? asset('storage/'.$this->logo_path) : null,
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toISO8601String(),
            'updated_at' => $this->updated_at?->toISO8601String(),
        ];
    }
}
