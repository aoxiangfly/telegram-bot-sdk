<?php

namespace Telegram\Bot\Laravel;

use Telegram\Bot\Api;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class TelegramServiceProvider
 *
 * @package Telegram\Bot\Laravel
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Holds path to Config File.
     *
     * @var string
     */
    protected $config_filepath;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            $this->config_filepath => config_path('telegram.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerTelegram($this->app);

        $this->config_filepath = __DIR__.'/config/telegram.php';

        $this->mergeConfigFrom($this->config_filepath, 'telegram');
    }

    /**
     * Initialize Telegram Bot SDK Library with Default Config.
     *
     * @param Application $app
     */
    protected function registerTelegram(Application $app)
    {
        $app->singleton(Api::class, function ($app) {
            $config = $app['config'];

            $telegram = new Api(
                $config->get('telegram.bot_token', false),
                $config->get('telegram.async_requests', false),
                $config->get('telegram.http_client_handler', null)
            );

            // Register Commands
            $telegram->addCommands($config->get('telegram.commands', []));

            return $telegram;
        });

        $app->alias(Api::class, 'telegram');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['telegram', Api::class];
    }
}
