<?php

namespace Tokenly\EventsPublisher;

use Exception;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Tokenly\EventsPublisher\Publisher;

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

        $this->app->bind(Publisher::class, function($app) {
            $queue_manager = app(QueueManager::class);
            $queue_connection = $queue_manager->connection(Config::get('events-publisher.queueConnection'));

            return new Publisher(
                $queue_connection,
                Config::get('events-publisher.queueName'),
                Config::get('events-publisher.mixpanelActive'),
                Config::get('events-publisher.keenActive'),
                Config::get('events-publisher.slackActive')
            );
        });

        // add artisan commands
        $this->commands([
            'Tokenly\EventsPublisher\Commands\SendTestEvent',
            'Tokenly\EventsPublisher\Commands\SendTestSlackNotification',
        ]);

    }

}

