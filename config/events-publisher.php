<?php

return [
    'queueConnection' => env('EVENT_PUBLISHER_QUEUE_CONNECTION', 'blockingbeanstalkd'),
    'queueName'       => env('EVENT_PUBLISHER_QUEUE_NAME',       'tkevents'),
    'mixpanelActive'  => env('MIXPANEL_EVENTS_ACTIVE',           true),
    'keenActive'      => env('KEEN_EVENTS_ACTIVE',               true),
    'slackActive'     => env('SLACK_NOTIFICATIONS_ACTIVE',       true),
];