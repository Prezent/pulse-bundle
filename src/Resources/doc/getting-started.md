Getting started
===============

The basic concept of the PulseBundle is very simple. Any time you want to track an event, simply
dispatch a PulseEvent on the Symfony event dispatcher. If you want to know aggregate statistics for
these events, query the repository.


Anatomy of a PulseEvent
-----------------------

A PulseEvent consists of a type name (string) and one or more aggregations. When the event is dispatched,
the counter for all the aggregations will be updated. Here is an example:

```php
use Prezent\PulseBundle\Event\PulseEvent;

$event = new PulseEvent('type.name', [
    'user' => $user->getId(),
]);
```

The `'type.name'` is the name of this event type. It will be aggretated on user ID. You can add multiple
aggregations and multiple IDs per aggregation. Example:

```php
// Aggregate by source
$event->addAggregation('source', ['web', 'organic']);

// Aggegate by group
foreach ($user->getGroups() as $group) {
    $event->addAggregation('group', $group->getId());
}
```


Tracking events
---------------

To track your event, just dispatch it on the event dispatcher.

```php
$dispatcher = $container->get('event_dispatcher');
$dispatcher->dispatch(PulseEvent::EVENT, $event);
```

When the event is dispatched, it will be stored in the database and all aggregations will be updated.
All events are also aggregated by day.


Querying statistics
-------------------

You can query the pulse repository for aggregate statistics. Start by creating a Query object:

```php
use Prezent\PulseBundle\Query\Query;

$query = new Query();
$query
    ->setStartDate(new \DateTime('-7 days'))
    ->setEndDate(new \DateTime())
;

$repository = $container->get('prezent_pulse.repository');

$result = $repository->query($query);
```

This will give you summed aggregate statistics over the last 7 days. Every row is a unique
aggegate key/id combination. Every column is a distinct event type. The result structure is
detailed in [Using results](using-results.md).

You can limit the event types or aggegates you want to search for:

```php
// Only search these event types
$query->setTypes(['type.1', 'type.2']);

// Only show user aggregates
$query->addFilter('user');

// Only show specific user aggregates
$query->addFilter('user', [1, 3, 4]);
```

Filters are not mutually exclusive. If you add a `'user'` filter and a `'group'` filter,
your result will contain aggregation rows for all users and all groups.


Resetting your aggregates
-------------------------

If for some reason your aggegarete statistics ever break, or if you have manually updated the `pulse_events`
table for some reason, then you can recalculate all aggregates using this console command:

`$ bin/console pulse:replay`
