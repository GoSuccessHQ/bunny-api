# `$stream->videos->resolutions()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}/resolutions`

Video resolutions info

## Signature

```php
public function resolutions(string $videoId): VideoResolutionsInfo
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |

## Returns

`VideoResolutionsInfo`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->resolutions(videoId: '00000000-0000-0000-0000-000000000000');
```
