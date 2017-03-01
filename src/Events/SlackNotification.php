<?php

namespace Tokenly\EventsPublisher\Events;

/*
 */
class SlackNotification
{

    var $notification_data;

    /**
     * Create a new notification_data instance.
     *
     * @return void
     */
    public function __construct($notification_data)
    {
        $this->notification_data  = $notification_data;
    }

}
