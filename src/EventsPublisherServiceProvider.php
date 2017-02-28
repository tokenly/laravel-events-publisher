<?php

namespace Tokenly\EventsPublisher;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/*
* EventsPublisherServiceProvider
*/
class EventsPublisherServiceProvider extends ServiceProvider
{

    public function boot() {

        $this->publishes([
            __DIR__.'/../config/events-publisher.php' => config_path('events-publisher.php'),
        ]);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/events-publisher.php', 'events-publisher'
        );

        $this->app->bind('Tokenly\EventsPublisher\Contracts\APIUserRepositoryContract', function($app) {
            return new Publisher(
                Config::get('events-publisher.queueConnection'),
                Config::get('events-publisher.queueName'),
                Config::get('events-publisher.mixpanelActive'),
                Config::get('events-publisher.keenActive'),
                Config::get('events-publisher.slackActive')
            );
        });

        // add artisan commands
        $this->commands([
            'Tokenly\LaravelApiProvider\Commands\NewAPIUserCommand',
        ]);

    }

}

