<?php

namespace Tokenly\EventsPublisher;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Tokenly\EventsPublisher\Events\PublishedEvent;

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
        ]);
    }

    public function sendMixpanelEvent($collection, $event) {
        if (!$this->mixpanel_active) { return; }

        $this->sendEventToBeanstalkQueue($event, [
            'jobType'    => 'mixpanel',
            'collection' => $collection,
        ]);
    }

    public function sendSlackEvent($data_or_title, $text_or_fields) {
        if (!$this->slack_active) { return; }

        if (is_array($text_or_fields)) {
            $fields = $text_or_fields;
        } else {
            $fields = [['title' => 'Description', 'value' => $text_or_fields]];
        }
        $fields = $this->normalizeSlackFields($fields);

        if (is_array($data_or_title)) {
            $event = $data_or_title;
        } else {
            $event = ['title' => $data_or_title];
        }

        $event['fields'] = $fields;

        if (!isset($event['color'])) { $event['color'] = 'good'; }

        $this->sendEventToBeanstalkQueue($event, [
            'jobType'    => 'slack',
        ]);
    }

    public function onEvent(PublishedEvent $event) {
        $event_data = $event->event_data;
        foreach ($event->providers as $provider) {
            switch ($provider) {
                case 'mixpanel':
                    $this->sendMixpanelEvent($event_data['title'], $event_data['event']);
                    break;
                case 'keen':
                    $this->sendKeenEvent($event_data['title'], $event_data['event']);
                    break;
                case 'slack':
                    $this->sendSlackEvent($this->firstAvailable($event_data, ['slackTitle', 'title']), $event_data['slackData']);
                    break;
                
                default:
                    throw new Exception("Unknown provider $provider", 1);
            }
        }
    }


    public function subscribe($events)
    {
        $events->listen(
            'Tokenly\EventsPublisher\Events\PublishedEvent',
            'Tokenly\EventsPublisher\Events\Publisher@onEvent'
        );
    }

    // ------------------------------------------------------------------------
    
    protected function normalizeSlackFields($fields_in) {
        $fields_out = [];
        foreach($fields_in as $field) {
            if (!isset($field['short'])) {
                $field['short'] = (strlen($field['value']) < 25);
            }
            $fields_out[] = $field;
        }
        return $fields_out;
    }

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

