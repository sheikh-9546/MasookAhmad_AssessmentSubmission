<?php

namespace App\Providers;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private function bootLongerConsoleRequest()
    {
        $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
            5000,
            function ($startedAt, $input, $status) {
                Log::warning('A command took longer than 5 seconds.');
            }
        );
    }

    private function bootLongerHttpRequest()
    {
        $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
            5000,
            function ($startedAt, $request, $response) {
                Log::warning('A request took longer than 5 seconds.');
            }
        );
    }

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
    public function boot()
    {

        $this->app->runningInConsole()
            ? $this->bootLongerConsoleRequest()
            : $this->bootLongerHttpRequest();
    }
}
