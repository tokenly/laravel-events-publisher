<?php

namespace Tokenly\EventsPublisher\Commands;

use Illuminate\Console\Command;
use Tokenly\EventsPublisher\Events\PublishedEvent;

class SendTestEvent extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'events:send-test-event 
        { -k|--keen : Send to keen }
        { -m|--mixpanel : Send to mixpanel }
        { -s|--slack : Send to slack }
        {title : The event title}
        {data : Event JSON}';


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

        $title    = $this->argument('title');
        $data     = json_decode($this->argument('data'), true);

        $keen     = $this->option('keen');
        $mixpanel = $this->option('mixpanel');
        $slack    = $this->option('slack');

        $providers = [];
        if ($keen) { $providers[] = 'keen'; }
        if ($mixpanel) { $providers[] = 'mixpanel'; }
        if ($slack) { $providers[] = 'slack'; }

        event(new PublishedEvent($providers, $data));

        $this->comment('done');
    }
}
