# `$stream->videos->heatmap()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}/heatmap`

Get Video Heatmap

Returns the attention heatmap for a specific video, showing relative viewer interest across the timeline. May be unavailable if the feature is disabled or there isn't enough viewing data.

## Signature

```php
public function heatmap(string $videoId): VideoHeatmap
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |

## Returns

`VideoHeatmap`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->heatmap(videoId: '00000000-0000-0000-0000-000000000000');
```
