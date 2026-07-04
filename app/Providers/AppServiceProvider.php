<?php

namespace App\Providers;

use App\Services\AI\AIProviderInterface;
use App\Services\AI\GeminiService;
use App\Services\AI\GroqService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIProviderInterface::class, function ($app) {
            $provider = config('services.ai.provider', 'gemini');
            return $provider === 'groq' ? new GroqService() : new GeminiService();
        });
    }

    public function boot(): void
    {
        app()->setLocale('es');
        Paginator::useBootstrapFive();
    }
}
