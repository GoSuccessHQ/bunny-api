# `$bunny->core->videoLibraries->drmStatistics()`

> Core Platform API · `GET /videolibrary/{id}/drm/statistics`

Get Video Library DRM Statistics

## Signature

```php
public function drmStatistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
): VideoLibraryDrmStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the video library for which the DRM statistics will be returned |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 14 days will be returned |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, current date will be used |

## Returns

`VideoLibraryDrmStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->drmStatistics(id: 123);
```
