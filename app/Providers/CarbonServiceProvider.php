<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class CarbonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::setLocale('id');
    }
}
