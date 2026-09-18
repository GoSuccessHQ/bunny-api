# `$stream->videos->repackage()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/repackage`

Repackage Video

Repackages the video from its existing .ts/.m4s segments without re-transcoding, primarily used to apply Enterprise DRM.

## Signature

```php
public function repackage(string $videoId, ?bool $keepOriginalFiles = null): Video
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$keepOriginalFiles` | `bool\|null` | no | Marks whether previous file versions should be kept in storage, allows for faster repackage later on. Default is true. |

## Returns

`Video`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->repackage(videoId: '00000000-0000-0000-0000-000000000000');
```
