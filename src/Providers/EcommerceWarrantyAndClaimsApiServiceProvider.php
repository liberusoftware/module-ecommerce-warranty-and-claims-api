<?php

declare(strict_types=1);

namespace Liberu\EcommerceWarrantyAndClaimsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class EcommerceWarrantyAndClaimsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
