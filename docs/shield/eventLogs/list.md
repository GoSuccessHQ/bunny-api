# `$bunny->shield->eventLogs->list()`

> Shield API · `GET /shield/event-logs/{shieldZoneId}/{date}/{continuationToken}`

Get a page of the event logs of one day.

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
