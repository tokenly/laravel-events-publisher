<?php

namespace Tokenly\EventsPublisher\Commands;

use Illuminate\Console\Command;
use Tokenly\EventsPublisher\Slack\Notification;

class SendTestSlackNotification extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'events:send-test-slack-notification 
        {text : Notification text}';


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

        $text  = $this->argument('text');

        Notification::plainText($text)->broadcast();

        $this->comment('done');
    }
}
