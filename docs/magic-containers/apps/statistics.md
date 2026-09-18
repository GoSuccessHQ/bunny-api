# `$bunny->magicContainers->apps->statistics()`

> Magic Containers API · `GET /apps/{appId}/statistics`

Get Application Statistics

Retrieves historical statistics for an application including CPU, RAM, traffic, latency, and volume usage over a specified time period.

## Signature

```php
public function statistics(
    string $appId,
    DateTimeInterface $fromDate,
    DataGranularity $granularity,
    ?DateTimeInterface $toDate = null,
): Statistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$fromDate` | `DateTimeInterface` | yes |  |
| `$granularity` | `DataGranularity` | yes |  |
| `$toDate` | `DateTimeInterface\|null` | no |  |

## Returns

`Statistics`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Enum\DataGranularity;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->statistics(appId: '00000000-0000-0000-0000-000000000000', fromDate: new DateTimeImmutable('-7 days'), granularity: DataGranularity::Daily);
```
