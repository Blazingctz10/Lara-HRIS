<?php

/**
 * PHP Intl compatibility layer for environments where the native intl extension is disabled.
 * Provides fallback for Illuminate\Support\Number and Filament tables.
 */

namespace Illuminate\Support;

if (! function_exists('Illuminate\Support\extension_loaded')) {
    function extension_loaded(string $extension): bool
    {
        if ($extension === 'intl') {
            return class_exists('\NumberFormatter') || class_exists('\Symfony\Polyfill\Intl\Icu\NumberFormatter') || \extension_loaded('intl');
        }

        return \extension_loaded($extension);
    }
}
