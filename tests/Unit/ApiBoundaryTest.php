<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ApiBoundaryTest extends TestCase
{
    public function test_api_owns_versioned_routes_and_has_no_application_namespace_dependency(): void
    {
        $route = file_get_contents(__DIR__.'/../../routes/api.php');
        self::assertStringContainsString("prefix('api/v1/ecommerce/warranty-and-claims')", $route);
        self::assertStringContainsString("auth:sanctum", $route);
        self::assertStringNotContainsString('App\\', $route);
    }
}
