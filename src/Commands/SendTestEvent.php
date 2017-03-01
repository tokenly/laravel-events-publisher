<?php

namespace Tokenly\EventsPublisher\Commands;

use Illuminate\Console\Command;
use Tokenly\EventsPublisher\Events\StatsEvent;

class SendTestEvent extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'events:send-test-event 
        { --k|keen : Send to keen }
        { --m|mixpanel : Send to mixpanel }
        {collection : The event collection}
        {event : Event JSON}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queues a test event to be sent';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $collection = $this->argument('collection');
        $event      = json_decode($this->argument('event'), true);

        $keen     = $this->option('keen');
        $mixpanel = $this->option('mixpanel');

        $providers = [];
        if ($keen) { $providers[] = StatsEvent::PROVIDER_KEEN; }
        if ($mixpanel) { $providers[] = StatsEvent::PROVIDER_MIXPANEL; }

        if ($providers) {
            event(new StatsEvent($collection, $event, $providers));
        }

        $this->comment('done');
    }
}
