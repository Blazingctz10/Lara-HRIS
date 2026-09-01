<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Observers\EmployeeObserver;
use App\Observers\LeaveRequestObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            function () {
                return new class implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse {
                    public function toResponse($request): \Illuminate\Http\RedirectResponse
                    {
                        return redirect('/');
                    }
                };
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
    }
}
