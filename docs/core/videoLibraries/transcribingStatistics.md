# `$bunny->core->videoLibraries->transcribingStatistics()`

> Core Platform API · `GET /videolibrary/{id}/transcribing/statistics`

Get Video Library Transcribing Statistics

## Signature

```php
public function transcribingStatistics(
    int $id,
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
): VideoLibraryTranscriptionStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the video library for which the transcribing statistics will be returned |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 14 days will be returned |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, current date will be used |

## Returns

`VideoLibraryTranscriptionStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->transcribingStatistics(id: 123);
```
