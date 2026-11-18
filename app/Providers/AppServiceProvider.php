<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Category\SeedDefaultCategories;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Model::shouldBeStrict($this->app->isLocal());

        Event::listen(function (Registered $event): void {
            if ($event->user instanceof User) {
                app(SeedDefaultCategories::class)($event->user);
            }
        });
    }
}
