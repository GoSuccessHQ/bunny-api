# `$stream->videos->uploadThumbnail()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/thumbnail`

Upload the thumbnail image.

## Signature

```php
public function uploadThumbnail(
    string $videoId,
    Stream|string $image,
    string $contentType = 'image/jpeg',
): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$image` | `string\|Stream` | yes | The image file. |
| `$contentType` | `string` | no | The image's media type. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->uploadThumbnail(videoId: '00000000-0000-0000-0000-000000000000', image: Stream::fromFile('path/to/file'));
```
