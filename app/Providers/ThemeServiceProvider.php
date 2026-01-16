<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $theme = session('theme', 'dark'); // Default to 'light' if not set

        // Set the theme in the configuration
        Config::set('custom.theme', $theme);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $currentTheme = 'dark-layout';
        if (Auth::check()) {
            $currentTheme = Auth::user()->theme;
        }
        View::share('theme', $currentTheme);
    }
}
