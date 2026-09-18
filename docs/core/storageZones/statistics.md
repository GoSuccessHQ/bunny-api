# `storageZones->statistics()`

> Core Platform API · `GET /storagezone/{id}/statistics`

Get Storage Zone Statistics

## Signature

```php
public function statistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
): StorageZoneStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the storage zone |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |

## Returns

`StorageZoneStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->statistics(id: 123);
```
