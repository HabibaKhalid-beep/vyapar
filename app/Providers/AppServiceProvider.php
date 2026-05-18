<?php

namespace App\Providers;

use Dotenv\Dotenv;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->syncRuntimeEnvironmentDatabaseConfig();
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Use Bootstrap 5 pagination styles by default (instead of Tailwind)
        Paginator::useBootstrapFive();
    }

    private function syncRuntimeEnvironmentDatabaseConfig(): void
    {
        if (!$this->app->configurationIsCached()) {
            return;
        }

        if (is_file(base_path('.env'))) {
            Dotenv::createMutable(base_path())->safeLoad();
        }

        $connection = $this->runtimeEnv('DB_CONNECTION');
        $host = $this->runtimeEnv('DB_HOST');
        $port = $this->runtimeEnv('DB_PORT');
        $database = $this->runtimeEnv('DB_DATABASE');
        $username = $this->runtimeEnv('DB_USERNAME');
        $password = $this->runtimeEnv('DB_PASSWORD');
        $sessionDriver = $this->runtimeEnv('SESSION_DRIVER');
        $sessionConnection = $this->runtimeEnv('SESSION_CONNECTION');

        if ($connection) {
            config(['database.default' => $connection]);
        }

        foreach (['mysql', 'mariadb'] as $driver) {
            if ($host !== null && $host !== '') {
                config(["database.connections.{$driver}.host" => $host]);
            }
            if ($port !== null && $port !== '') {
                config(["database.connections.{$driver}.port" => $port]);
            }
            if ($database !== null && $database !== '') {
                config(["database.connections.{$driver}.database" => $database]);
            }
            if ($username !== null && $username !== '') {
                config(["database.connections.{$driver}.username" => $username]);
            }
            if ($password !== null) {
                config(["database.connections.{$driver}.password" => $password]);
            }
        }

        if ($sessionDriver) {
            config(['session.driver' => $sessionDriver]);
        }

        if ($sessionConnection !== null && $sessionConnection !== '') {
            config(['session.connection' => $sessionConnection]);
        }

        if ($this->app->bound('db')) {
            DB::purge();
        }
    }

    private function runtimeEnv(string $key): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return $value === false ? null : $value;
    }
}
