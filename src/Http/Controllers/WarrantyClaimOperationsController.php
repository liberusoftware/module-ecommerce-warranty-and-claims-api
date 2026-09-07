<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Liberu\EcommerceWarrantyAndClaims\Actions\RecordWarrantyException;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyClaim;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyException;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyLabel;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyManifest;
use Liberu\EcommerceWarrantyAndClaims\Models\WarrantyScan;

final class WarrantyClaimOperationsController
{
    public function scans(Request $request, WarrantyClaim $claim): mixed { return $this->create($request, $claim, WarrantyScan::class, ['scan_type', 'reference']); }
    public function labels(Request $request, WarrantyClaim $claim): mixed { return $this->create($request, $claim, WarrantyLabel::class, ['label_type']); }
    public function manifests(Request $request, WarrantyClaim $claim): mixed { return $this->create($request, $claim, WarrantyManifest::class, ['manifest_number', 'items']); }

    public function exceptions(Request $request, WarrantyClaim $claim, RecordWarrantyException $record): mixed
    {
        Gate::authorize('update', $claim);
        $data = $request->validate(['code' => ['required', 'string', 'max:100'], 'message' => ['required', 'string', 'max:5000'], 'retryable' => ['boolean'], 'context' => ['nullable', 'array']]);
        return response()->json(['data' => $record->execute($data + ['warranty_claim_id' => $claim->id, 'tenant_id' => $claim->tenant_id])], 201);
    }

    private function create(Request $request, WarrantyClaim $claim, string $model, array $required): mixed
    {
        Gate::authorize('update', $claim);
        $rules = array_fill_keys($required, ['required']);
        $rules['metadata'] = ['nullable', 'array'];
        if ($model === WarrantyManifest::class) { $rules['items'] = ['required', 'array']; }
        $data = $request->validate($rules);
        $record = $model::create($data + ['warranty_claim_id' => $claim->id, 'tenant_id' => $claim->tenant_id, 'status' => 'pending', 'scanned_at' => now(), 'issued_at' => now()]);
        return response()->json(['data' => $record], 201);
    }
}
