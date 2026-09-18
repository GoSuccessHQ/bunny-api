# `$stream->videos->reencode()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/reencode`

Reencode Video

Re-encodes the video from its stored original file. Requires the original file to still be present in storage. If the library has transcribing enabled, this also re-queues (and re-bills) transcription.

## Signature

```php
public function reencode(string $videoId): Video
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

$result = $stream->videos->reencode(videoId: '00000000-0000-0000-0000-000000000000');
```
