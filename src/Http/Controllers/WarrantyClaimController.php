<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers;

use Liberu\EcommerceWarrantyAndClaims\Actions\SubmitWarrantyClaim;
use Liberu\EcommerceWarrantyAndClaims\Actions\TransitionWarrantyClaim;
use Liberu\EcommerceWarrantyAndClaims\Enums\ClaimDecision;
use Liberu\EcommerceWarrantyAndClaims\Enums\ClaimStatus;
use Liberu\EcommerceWarrantyAndClaimsApi\Http\Resources\WarrantyClaimResource;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyClaim;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class WarrantyClaimController
{
    public function index(Request $request): mixed
    {
        $query = WarrantyClaim::query()->with('registration')->latest();
        if (! $this->staff($request)) {
            $query->where('customer_id', (string) $request->user()->getAuthIdentifier());
        }
        return WarrantyClaimResource::collection($query->paginate(min((int) $request->integer('per_page', 25), 100)));
    }

    public function show(Request $request, WarrantyClaim $claim): WarrantyClaimResource
    {
        Gate::authorize('view', $claim);
        return new WarrantyClaimResource($claim->load(['registration', 'history', 'provider']));
    }

    public function store(Request $request, SubmitWarrantyClaim $submit): WarrantyClaimResource|JsonResponse
    {
        Gate::authorize('create', WarrantyClaim::class);
        $data = $request->validate([
            'registration_id' => ['required', 'integer', 'exists:warranty_registrations,id'],
            'issue' => ['required', 'string', 'max:10000'],
            'evidence' => ['nullable', 'array', 'max:20'],
            'troubleshooting' => ['nullable', 'string', 'max:10000'],
        ]);
        $registration = WarrantyRegistration::query()->findOrFail($data['registration_id']);
        Gate::authorize('view', $registration->claims()->first() ?? new WarrantyClaim(['customer_id' => $registration->customer_id]));
        $claim = $submit->execute($data + ['registration' => $registration, 'customer_id' => (string) $request->user()->getAuthIdentifier(), 'actor_id' => (string) $request->user()->getAuthIdentifier()]);
        return (new WarrantyClaimResource($claim))->response()->setStatusCode(201);
    }

    public function update(Request $request, WarrantyClaim $claim, TransitionWarrantyClaim $transition): WarrantyClaimResource|JsonResponse
    {
        Gate::authorize('update', $claim);
        $data = $request->validate(['status' => ['required', 'string'], 'decision' => ['nullable', 'string'], 'note' => ['nullable', 'string', 'max:5000']]);
        $status = ClaimStatus::tryFrom($data['status']);
        if (! $status) {
            return response()->json(['type' => 'about:blank', 'title' => 'Invalid status', 'status' => 422, 'detail' => 'The requested claim status is not supported.'], 422);
        }
        $decision = isset($data['decision']) ? ClaimDecision::tryFrom($data['decision']) : null;
        return new WarrantyClaimResource($transition->execute($claim, $status, $decision, (string) $request->user()->getAuthIdentifier(), $data['note'] ?? null));
    }

    private function staff(Request $request): bool
    {
        return $request->user()->hasAnyRole(['admin', 'staff', 'super-admin']);
    }
}
