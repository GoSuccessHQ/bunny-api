# `$bunny->shield->eventLogs->search()`

> Shield API · `POST /shield/event-logs/{shieldZoneId}/search`

Search, filter and group the event logs of a time window.

## Signature

```php
public function search(
    int $shieldZoneId,
    DateTimeInterface $from,
    DateTimeInterface $to,
    ?string $query = null,
    array $filters = [],
    array $groupBy = [],
    ?int $buckets = null,
    int $page = 0,
    ?int $pageSize = null,
): EventLogSearchResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$from` | `DateTimeInterface` | yes | The start of the window. |
| `$to` | `DateTimeInterface` | yes | The end of the window; after $from and within the last 72 hours. |
| `$query` | `string\|null` | no | Free text searched in IP, rule ID, URL, user agent and rule name. |
| `$filters` | `list<EventLogFilter>` | no | Filters, combined with AND. |
| `$groupBy` | `list<string>` | no | Dimensions to group by, in order, e.g. `['ip', 'ja4']`: feature, ruleId, ip, ja4, ua, url, asn, country or action. Empty for rows. |
| `$buckets` | `int\|null` | no | With grouping: the number of time buckets of each group's sparkline, up to 500; 0 or null for none. |
| `$page` | `int` | no | The page to return, starting at 0. |
| `$pageSize` | `int\|null` | no | Rows or groups per page, 1 to 500; 50 by default. |

## Returns

`EventLogSearchResult`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->eventLogs->search(shieldZoneId: 123, from: new DateTimeImmutable('-7 days'), to: new DateTimeImmutable('-7 days'));
```
