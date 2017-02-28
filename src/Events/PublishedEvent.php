<?php

namespace Tokenly\EventsPublisher\Events;

class PublishedEvent
{

    var $providers;
    var $event_data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($providers, $event_data)
    {
        $this->providers  = $providers;
        $this->event_data = $event_data;
    }

}
