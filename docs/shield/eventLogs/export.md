# `$bunny->shield->eventLogs->export()`

> Shield API · `POST /shield/event-logs/{shieldZoneId}/export`

Export the filtered event logs of a time window as CSV.

## Signature

```php
public function export(
    int $shieldZoneId,
    DateTimeInterface $from,
    DateTimeInterface $to,
    ?string $query = null,
    array $filters = [],
    ?Stream $sink = null,
): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$from` | `DateTimeInterface` | yes | The start of the window. |
| `$to` | `DateTimeInterface` | yes | The end of the window; after $from and within the last 72 hours. |
| `$query` | `string\|null` | no | Free text searched in IP, rule ID, URL, user agent and rule name. |
| `$filters` | `list<EventLogFilter>` | no | Filters, combined with AND. |
| `$sink` | `Stream\|null` | no | Write the CSV into this stream instead of returning it, e.g. `Stream::fromFile('events.csv', 'wb')`. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->eventLogs->export(shieldZoneId: 123, from: new DateTimeImmutable('-7 days'), to: new DateTimeImmutable('-7 days'));
```
