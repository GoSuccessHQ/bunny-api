# `$bunny->shield->eventLogs->list()`

> Shield API · `GET /shield/event-logs/{shieldZoneId}/{date}/{continuationToken}`

Get a page of the event logs of one day.

Only the day counts, taken in UTC; its time is ignored. bunny.net keeps the event logs of today and the two days before (verified live) and rejects older days with invalid_datetime_window.event_logs.

The first page is requested without a continuation token (verified live); each further page with the token of the previous one.

## Signature

```php
public function list(
    int $shieldZoneId,
    DateTimeInterface $date,
    ?string $continuationToken = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$date` | `DateTimeInterface` | yes | The day, taken in UTC. |
| `$continuationToken` | `string\|null` | no | The position returned by the previous page; null for the first page. |

## Returns

`Page<EventLog>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->eventLogs->list(shieldZoneId: 123, date: new DateTimeImmutable('-7 days'));
```
