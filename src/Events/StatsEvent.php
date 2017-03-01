<?php

namespace Tokenly\EventsPublisher\Events;

class StatsEvent
{

    var $providers;
    var $event;

    const PROVIDER_KEEN     = 'keen';
    const PROVIDER_MIXPANEL = 'mixpanel';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($collection, $event, $providers=null)
    {
        $this->collection = $collection;
        $this->event      = $event;

        if ($providers === null) {
            $providers = [self::PROVIDER_KEEN, self::PROVIDER_MIXPANEL];
        }
        $this->providers  = $providers;
    }

}
