<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class PrinterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('printer', function ($app) {
            $connector = new WindowsPrintConnector('POS');
            return new Printer($connector);
        });
    }

    public function boot()
    {
        //
    }
}
