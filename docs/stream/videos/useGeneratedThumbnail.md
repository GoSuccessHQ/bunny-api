# `$stream->videos->useGeneratedThumbnail()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/thumbnail`

Use one of the five thumbnails generated while encoding.

## Signature

```php
public function useGeneratedThumbnail(string $videoId, int $number): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$number` | `int` | yes | The generated thumbnail, 1 to 5. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->useGeneratedThumbnail(videoId: '00000000-0000-0000-0000-000000000000', number: 123);
```
