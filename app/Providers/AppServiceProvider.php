<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\CategoryRepository::class,
            \App\Repositories\EloquentCategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\ProductRepository::class,
            \App\Repositories\EloquentProductRepository::class
        );

        $this->app->bind(
            \App\Repositories\OrderRepository::class,
            \App\Repositories\EloquentOrderRepository::class
        );

        $this->app->bind(
            \App\Repositories\UserRepository::class,
            \App\Repositories\EloquentUserRepository::class
        );

        $this->app->bind(
            \App\Repositories\FeedbackRepository::class,
            \App\Repositories\EloquentFeedbackRepository::class
        );

        $this->app->bind(
            \App\Repositories\NotificationRepository::class,
            \App\Repositories\EloquentNotificationRepository::class
        );

        $this->app->singleton(\App\Services\ShippingService::class, function ($app) {
            return new \App\Services\ShippingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
    }
}
