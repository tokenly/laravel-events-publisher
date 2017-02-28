An events publisher for Laravel.  Pushes event jobs to a beanstalk queue.

### Installation

- `composer require tokenly/laravel-events-publisher`
- Add `Tokenly\EventsPublisher\EventsPublisherServiceProvider::class` to your list of service providers
- Add `Tokenly\EventsPublisher\Publisher::class` to your list of event subscribers in your EventServiceProvider class

