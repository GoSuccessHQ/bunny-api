# `$bunny->shield->eventLogs->all()`

> Shield API · `GET /shield/event-logs/{shieldZoneId}/{date}/{continuationToken}`

Iterate lazily over all event logs of one day, across all pages.

Only the day counts, taken in UTC; bunny.net keeps the event logs of today and the two days before, see list().

## Signature

```php
public function all(int $shieldZoneId, DateTimeInterface $date): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$date` | `DateTimeInterface` | yes | The day, taken in UTC. |

## Returns

`Paginator<EventLog>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->shield->eventLogs->all(shieldZoneId: 123, date: new DateTimeImmutable('-7 days')) as $item) {
    // ...
}
```
