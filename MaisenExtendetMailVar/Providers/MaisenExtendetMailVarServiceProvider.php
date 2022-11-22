<?php

namespace Modules\MaisenExtendetMailVar\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

define('MAISEN_EXTENDET_MAIL_VAR', 'maisenextendetmailvar');

class MaisenExtendetMailVarServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(MAISEN_EXTENDET_MAIL_VAR).'/js/module.js';
            return $javascripts;
        });
        
        \Eventy::addFilter('mail_vars.replace', function($vars, $data) {
            if (!empty($data['conversation'])) {
                $data['conversation']->load(['threads' => function ($query) {
                    $query->latest('created_at')->first();
                }]);
                $first_thread = $data['conversation']->threads()->first();
                $vars['{%conversation.firstmessage%}'] = ($first_thread) ? $first_thread->body : '';
            }

            return $vars;
        }, 20, 2);

        // JavaScript in the bottom
        \Eventy::addAction('javascript', function() {
            if (\Route::is('mailboxes.auto_reply')) {
                echo 'maisenextendetmailvarInit();';
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('maisenextendetmailvar.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'maisenextendetmailvar'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/maisenextendetmailvar');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/maisenextendetmailvar';
        }, \Config::get('view.paths')), [$sourcePath]), 'maisenextendetmailvar');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
