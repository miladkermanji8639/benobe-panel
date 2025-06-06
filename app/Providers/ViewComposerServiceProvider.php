<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ارسال متغیر permissions به تمام ویوهایی که sidebar.blade.php را include کرده‌اند
        View::composer('dr.panel.layouts.partials.sidebar', function ($view) {
            $permissions = [];
            if (Auth::guard('secretary')->check()) {
                $secretary = Auth::guard('secretary')->user();
                $permissionRecord = $secretary->permissions()->first();
                $permissions = $permissionRecord ? ($permissionRecord->permissions ?? []) : [];
            }

            $view->with('permissions', $permissions);
        });
    }
}
