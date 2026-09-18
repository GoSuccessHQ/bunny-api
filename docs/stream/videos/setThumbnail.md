# `$stream->videos->setThumbnail()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/thumbnail`

Set the thumbnail to an image bunny.net fetches from a URL.

## Signature

```php
public function setThumbnail(string $videoId, string $url): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$url` | `string` | yes | A publicly reachable image URL. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->setThumbnail(videoId: '00000000-0000-0000-0000-000000000000', url: 'https://example.com/');
```
