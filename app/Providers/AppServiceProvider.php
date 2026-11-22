<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\AdminNotificationService;

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
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        View::composer('admin.layout', function ($view) {
            try {
                $notificationService = app(AdminNotificationService::class);
                $view->with('adminNotifications', $notificationService->getNotifications());
            } catch (\Throwable $e) {
                $view->with('adminNotifications', [
                    'urgent_count'     => 0,
                    'field_count'      => 0,
                    'total_count'      => 0,
                    'action_required'  => collect(),
                    'field_operations' => collect(),
                ]);
            }
        });
    }
}
