# `$stream->videos->storageSize()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}/storage`

Get video storage size info

Requires the full API key of the library; the read-only key is rejected with 403 Forbidden.

## Signature

```php
public function storageSize(string $videoId): VideoStorageSize
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |

## Returns

`VideoStorageSize`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->storageSize(videoId: '00000000-0000-0000-0000-000000000000');
```
