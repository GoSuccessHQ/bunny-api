# `$stream->videos->fetch()`

> Stream API · `POST /library/{libraryId}/videos/fetch`

Create a video from a URL: bunny.net downloads and encodes the file.

The specification documents a bare status response, but the API also returns the GUID of the new video, which bunny.net's own CLI relies on.

## Signature

```php
public function fetch(
    string $url,
    ?string $title = null,
    ?string $collectionId = null,
    ?int $thumbnailTime = null,
    ?array $headers = null,
): ?string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$url` | `string` | yes | The URL to download the video from. |
| `$title` | `string\|null` | no | The title of the video; defaults to the file name in the URL. |
| `$collectionId` | `string\|null` | no | The ID of the collection to put the video in. |
| `$thumbnailTime` | `int\|null` | no | The video time in milliseconds to take the thumbnail from. |
| `$headers` | `?array` | no |  |

## Returns

`string|null`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->fetch(url: 'https://example.com/');
```
