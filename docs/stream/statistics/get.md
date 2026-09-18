# `$stream->statistics->get()`

> Stream API · `GET /library/{libraryId}/statistics`

Get Video Statistics

Returns time-series views and watch time, plus country-level aggregates, at the library level or for a specific video. Control the time window with dateFrom/dateTo and the granularity with hourly. Basic safeguards prevent spam and bot inflation by de-duplicating sessions and ignoring obviously invalid events.

## Signature

```php
public function get(
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $hourly = null,
    ?string $videoGuid = null,
): VideoStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$dateFrom` | `DateTimeInterface\|null` | no | Optional start of the time range (UTC). If omitted or invalid, the last 30 days are returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | Optional end of the time range (UTC). If omitted with a valid start, defaults to now; otherwise the last 30 days are returned. |
| `$hourly` | `bool\|null` | no | Optional. If true, returns hourly data; otherwise daily (UTC). Default is daily. |
| `$videoGuid` | `string\|null` | no | Optional video GUID to filter results. When omitted, returns library-level aggregates. |

## Returns

`VideoStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->statistics->get();
```
