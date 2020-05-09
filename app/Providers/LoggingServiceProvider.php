<?php

namespace App\Providers;

use DB;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (in_array($this->app->environment(), ['testing', 'local'])) {
            DB::listen(static function ($query) {
                $logger = new Logger('DB query', [
                    new StreamHandler(
                        storage_path('logs/db.log')
                    ),
                ]);

                $logger->debug('SQL', [
                    $query->sql,
                    $query->bindings,
                    $query->time,
                ]);
            });
        }
    }
}
