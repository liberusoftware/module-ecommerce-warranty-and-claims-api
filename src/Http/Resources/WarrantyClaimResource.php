<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WarrantyClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => 'ecommerce-warranty-and-claims',
            'attributes' => [
                'claim_number' => $this->claim_number,
                'customer_id' => (string) $this->customer_id,
                'registration_id' => (string) $this->warranty_registration_id,
                'issue' => $this->issue,
                'evidence' => $this->evidence ?? [],
                'troubleshooting' => $this->troubleshooting,
                'status' => $this->status?->value ?? $this->status,
                'decision' => $this->decision?->value ?? $this->decision,
                'resolved_at' => $this->resolved_at?->toISOString(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
            'relationships' => [
                'history' => $this->whenLoaded('history', fn () => $this->history->map(fn ($event) => [
                    'type' => $event->type,
                    'from_status' => $event->from_status,
                    'to_status' => $event->to_status,
                    'note' => $event->note,
                    'occurred_at' => $event->occurred_at?->toISOString(),
                ])->values()),
            ],
        ];
    }
}
