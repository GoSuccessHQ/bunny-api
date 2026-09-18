# `$stream->videos->get()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}`

Get Video

## Signature

```php
public function get(string $videoId): Video
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |

## Returns

`Video`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->get(videoId: '00000000-0000-0000-0000-000000000000');
```
