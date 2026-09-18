# `storageZones->egressStatistics()`

> Core Platform API · `GET /storagezone/{id}/statistics/egress`

Get Storage Zone Egress Statistics

## Signature

```php
public function egressStatistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?bool $hourly = null,
): StorageZoneEgressStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the storage zone |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |

## Returns

`StorageZoneEgressStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->egressStatistics(id: 123);
```
