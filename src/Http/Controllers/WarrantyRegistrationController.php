<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Liberu\EcommerceWarrantyAndClaims\Actions\RegisterWarranty;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyRegistration;

final class WarrantyRegistrationController
{
    public function index(Request $request): mixed
    {
        return WarrantyRegistration::query()->where('customer_id', (string) $request->user()->getAuthIdentifier())->latest()->paginate(min($request->integer('per_page', 25), 100));
    }

    public function store(Request $request, RegisterWarranty $register): mixed
    {
        $data = $request->validate([
            'warranty_term_id' => ['required', 'integer', 'exists:warranty_terms,id'],
            'product_id' => ['required', 'string', 'max:255'],
            'order_id' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchased_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ]);
        Gate::authorize('create', WarrantyRegistration::class);
        $registration = $register->execute($data + ['customer_id' => (string) $request->user()->getAuthIdentifier()]);
        return response()->json(['data' => $registration], 201);
    }
}
