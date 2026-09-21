<?php

namespace App\Providers;

use App\Services\AiModeration\AiModerationServiceInterface;
use App\Services\AiModeration\OpenRouterModerationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\ContentPolicy;
use App\Models\Content;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiModerationServiceInterface::class, OpenRouterModerationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Content::class, ContentPolicy::class);
    }
}
