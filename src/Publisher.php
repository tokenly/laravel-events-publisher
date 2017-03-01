<?php

namespace Tokenly\EventsPublisher;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Tokenly\EventsPublisher\Events\SlackNotification;
use Tokenly\EventsPublisher\Events\StatsEvent;

/*
* Publisher
*/
class Publisher
{

    public function __construct($queue_connection, $queue_name, $mixpanel_active, $keen_active, $slack_active) {
        $this->queue_connection = $queue_connection;
        $this->queue_name       = $queue_name;
        $this->keen_active      = $keen_active;
        $this->mixpanel_active  = $mixpanel_active;
        $this->slack_active     = $slack_active;
    }

    public function sendKeenEvent($collection, $event) {
        if (!$this->keen_active) { return; }

        $this->sendEventToBeanstalkQueue($event, [
            'jobType'    => 'keen',
            'collection' => $collection,
            'attempt'    => 0,
        ]);
    }

    public function sendMixpanelEvent($collection, $event) {
        if (!$this->mixpanel_active) { return; }

        $this->sendEventToBeanstalkQueue($event, [
            'jobType'    => 'mixpanel',
            'collection' => $collection,
            'attempt'    => 0,
        ]);
    }

    public function sendSlackNotification($message_data) {
        if (!$this->slack_active) { return; }

        $this->sendEventToBeanstalkQueue($message_data, [
            'jobType'    => 'slack',
            'attempt'    => 0,
        ]);

     }

    public function onEvent(StatsEvent $event) {
        $collection = $event->collection;
        $event_data = $event->event;
        foreach ($event->providers as $provider) {
            switch ($provider) {
                case 'mixpanel':
                    $this->sendMixpanelEvent($collection, $event_data);
                    break;

                case 'keen':
                    $this->sendKeenEvent($collection, $event_data);
                    break;
                
                default:
                    throw new Exception("Unknown provider $provider", 1);
            }
        }
    }

    public function onSlackNotification(SlackNotification $event) {
        $this->sendSlackNotification($event->notification_data);
    }


    public function subscribe($events)
    {
        $events->listen(
            'Tokenly\EventsPublisher\Events\StatsEvent',
            'Tokenly\EventsPublisher\Publisher@onEvent'
        );
        $events->listen(
            'Tokenly\EventsPublisher\Events\SlackNotification',
            'Tokenly\EventsPublisher\Publisher@onSlackNotification'
        );
    }

    // ------------------------------------------------------------------------
    
    protected function sendEventToBeanstalkQueue($event, $meta) {
        $entry = [
            'meta' => $meta,
            'data' => $event,
        ];

        // put notification in the queue
        $this->queue_connection->pushRaw(json_encode($entry), $this->queue_name);
    }

    protected function firstAvailable($arr, $keys) {
        foreach($keys as $key) {
            if (isset($arr[$key])) {
                return $arr[$key];
            }
        }

        return null;
    }

}

