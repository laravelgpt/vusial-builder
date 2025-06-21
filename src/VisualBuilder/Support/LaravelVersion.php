<?php

namespace LaravelBuilder\VisualBuilder\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class LaravelVersion
{
    /**
     * Get the current Laravel version.
     */
    public static function getVersion(): string
    {
        return App::version();
    }

    /**
     * Check if the current version is Laravel 10.
     */
    public static function isLaravel10(): bool
    {
        return version_compare(self::getVersion(), '10.0.0', '>=') && 
               version_compare(self::getVersion(), '11.0.0', '<');
    }

    /**
     * Check if the current version is Laravel 11.
     */
    public static function isLaravel11(): bool
    {
        return version_compare(self::getVersion(), '11.0.0', '>=') && 
               version_compare(self::getVersion(), '12.0.0', '<');
    }

    /**
     * Check if the current version is Laravel 12.
     */
    public static function isLaravel12(): bool
    {
        return version_compare(self::getVersion(), '12.0.0', '>=');
    }

    /**
     * Get the appropriate service provider based on Laravel version.
     */
    public static function getServiceProvider(): string
    {
        if (self::isLaravel12()) {
            return \LaravelBuilder\VisualBuilder\Providers\Laravel12ServiceProvider::class;
        }

        if (self::isLaravel11()) {
            return \LaravelBuilder\VisualBuilder\Providers\Laravel11ServiceProvider::class;
        }

        return \LaravelBuilder\VisualBuilder\Providers\Laravel10ServiceProvider::class;
    }

    /**
     * Get the appropriate bootstrap file based on Laravel version.
     */
    public static function getBootstrapFile(): string
    {
        if (self::isLaravel11() || self::isLaravel12()) {
            return 'bootstrap/app.php';
        }

        return 'bootstrap/app.php';
    }

    /**
     * Check if Pennant is available.
     */
    public static function hasPennant(): bool
    {
        return class_exists('Laravel\Pennant\Feature');
    }

    /**
     * Check if Reverb is available.
     */
    public static function hasReverb(): bool
    {
        return class_exists('Laravel\Reverb\ReverbServiceProvider');
    }

    /**
     * Check if Volt is available.
     */
    public static function hasVolt(): bool
    {
        return class_exists('Laravel\Volt\Volt');
    }

    /**
     * Check if Prompts is available.
     */
    public static function hasPrompts(): bool
    {
        return class_exists('Laravel\Prompts\Prompt');
    }

    /**
     * Get the minimum PHP version required.
     */
    public static function getMinimumPhpVersion(): string
    {
        if (self::isLaravel12()) {
            return '8.2.0';
        }

        if (self::isLaravel11()) {
            return '8.2.0';
        }

        return '8.1.0';
    }

    /**
     * Get the recommended PHP version.
     */
    public static function getRecommendedPhpVersion(): string
    {
        if (self::isLaravel12()) {
            return '8.3.0';
        }

        if (self::isLaravel11()) {
            return '8.2.0';
        }

        return '8.1.0';
    }
} 