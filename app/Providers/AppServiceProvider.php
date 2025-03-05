<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use App\Models\Store;
use Illuminate\Support\Facades\View;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;
use App\Models\Order;
use App\Models\User;
use App\Observers\IncomingOrder;
use App\Observers\Order as ObserversOrder;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(IncomingOrder::class);
        User::observe(UserObserver::class);

        User::deleting(function ($user) {
            if ($user->email == 'chaidaar@genzproject.my.id') {
                return false;
            }
        });

        View::composer('*', function ($view) {
            $view->with('store', Store::first());
        });
        OrderItem::observe(OrderItemObserver::class);
    }
}
