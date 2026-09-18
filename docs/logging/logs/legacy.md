# `$bunny->logging->logs->legacy()`

> CDN Logging API · `GET /{date}/{pullZoneId}.log`

Download the log of one day through the legacy v1 endpoint.

bunny.net keeps v1 for existing integrations; list() and all() filter on the server and return structured entries. The file is transferred compressed, stored in a temporary stream and parsed while you iterate.

## Signature

```php
public function legacy(
    int $pullZoneId,
    DateTimeInterface $date,
    ?int $start = null,
    ?int $end = null,
    ?string $sort = null,
    ?string $status = null,
    ?string $search = null,
): LegacyLog
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the pull zone. |
| `$date` | `DateTimeInterface` | yes | The day, taken in UTC; logs are retained for 3 days. |
| `$start` | `int\|null` | no | The number of lines to skip. |
| `$end` | `int\|null` | no | The index of the last line to return. |
| `$sort` | `string\|null` | no | `asc` or `desc` (the default). |
| `$status` | `string\|null` | no | The status classes to include, e.g. `4,5` (default `2,3,4,5`). |
| `$search` | `string\|null` | no | Only lines containing this text. |

## Returns

`LegacyLog`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->logging->logs->legacy(pullZoneId: 123, date: new DateTimeImmutable('-7 days'));
```
