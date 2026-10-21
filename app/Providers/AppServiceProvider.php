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
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && $dbPath !== ':memory:') {
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                if (!file_exists($dbPath)) {
                    @touch($dbPath);
                }
                @chmod($dir, 0777);
                @chmod($dbPath, 0666);

                try {
                    \Illuminate\Support\Facades\DB::statement('PRAGMA journal_mode = WAL;');
                    \Illuminate\Support\Facades\DB::statement('PRAGMA synchronous = NORMAL;');
                    \Illuminate\Support\Facades\DB::statement('PRAGMA cache_size = -20000;');
                    \Illuminate\Support\Facades\DB::statement('PRAGMA temp_store = MEMORY;');
                } catch (\Throwable $e) {
                    // Ignore during early boot/migration
                }
            }
        }

        if (config('app.env') === 'production' || env('APP_ENV') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Employee::observe(EmployeeObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
    }
}
