<?php

namespace App\Providers;

use App\Models\Batch;
use App\Models\Product;
use App\Policies\BatchPolicy;
use App\Policies\ProductPolicy;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentIcon::register([
            'panels::sidebar.collapse-button' => 'heroicon-o-bars-3',
            'panels::sidebar.expand-button' => 'heroicon-o-bars-3',
        ]);

        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Batch::class, BatchPolicy::class);
    }
}
