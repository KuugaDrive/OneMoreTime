<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use App\Policies\PackingListPolicy;
use App\Policies\OperasionalPolicy;
use App\Policies\KomisiPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(PackingList::class, PackingListPolicy::class);
        Gate::policy(Operasional::class, OperasionalPolicy::class);
        Gate::policy(Komisi::class, KomisiPolicy::class);
    }
}
