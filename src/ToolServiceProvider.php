<?php

namespace NovaConversationLog;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use NovaConversationLog\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-conversation-log');

        $this->app->booted(function () {
            $this->routes();
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }
        Route::middleware(['nova', Authorize::class])
                ->prefix('admin/conversation-log')
                ->group(__DIR__ . '/../routes/api.php');
    }

    public function register()
    {
    }
}
