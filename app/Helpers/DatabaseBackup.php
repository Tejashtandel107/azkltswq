<?php

namespace App\Helpers;

use Artisan;

class DatabaseBackup
{
    public function __construct()
    {
        //
    }

    public function artisanClear()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
    }

    public function backup()
    {
        Artisan::call('backup:run', ['--only-db' => true]);
    }

    public function cleanBackups()
    {
        Artisan::call('backup:clean');
    }
}
