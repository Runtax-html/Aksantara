<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && !str_starts_with($dbPath, ':memory:')) {
                $dbDir = dirname($dbPath);
                if (is_dir($dbDir)) {
                    @chmod($dbDir, 0777);
                }
                if (file_exists($dbPath)) {
                    @chmod($dbPath, 0666);
                } else {
                    @touch($dbPath);
                    @chmod($dbPath, 0666);
                }

                $testFile = $dbDir . '/.write_test_' . uniqid();
                $canWriteDir = @file_put_contents($testFile, 'test') !== false;
                if ($canWriteDir) {
                    @unlink($testFile);
                }

                // If SQLite directory or file is not writable in container, or storage db already exists, redirect to storage/
                $storageDb = storage_path('database.sqlite');
                if (!$canWriteDir || !is_writable($dbPath) || (file_exists($storageDb) && $dbPath !== $storageDb)) {
                    $storageDir = dirname($storageDb);
                    @chmod($storageDir, 0777);
                    if (!file_exists($storageDb) && file_exists($dbPath)) {
                        @copy($dbPath, $storageDb);
                    } elseif (!file_exists($storageDb)) {
                        @touch($storageDb);
                    }
                    @chmod($storageDb, 0666);
                    config(['database.connections.sqlite.database' => $storageDb]);
                    \Illuminate\Support\Facades\DB::purge('sqlite');
                }
            }
        }
    }
}
