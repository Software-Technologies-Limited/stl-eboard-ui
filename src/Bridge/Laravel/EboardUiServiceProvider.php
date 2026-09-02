<?php

declare(strict_types=1);

namespace Stl\EboardUi\Bridge\Laravel;

if (class_exists(\Illuminate\Support\ServiceProvider::class)) {
    final class EboardUiServiceProvider extends \Illuminate\Support\ServiceProvider
    {
        public function boot(): void
        {
            $this->publishes([
                dirname(__DIR__, 3).'/resources/css/eboard-ui.css' => public_path('vendor/eboard-ui/eboard-ui.css'),
                dirname(__DIR__, 3).'/resources/css/eboard-ui-theme.css' => public_path('vendor/eboard-ui/eboard-ui-theme.css'),
                dirname(__DIR__, 3).'/resources/js/eboard-ui.js' => public_path('vendor/eboard-ui/eboard-ui.js'),
            ], 'eboard-ui-assets');
        }
    }
}
