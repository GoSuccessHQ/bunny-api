# `$stream->videos->addOutputCodec()`

> Stream API · `PUT /library/{libraryId}/videos/{videoId}/outputs/{outputCodecId}`

Add output codec to video

Encodes and adds an additional output codec to an already-encoded video. Requires Premium Encoding to be enabled for the library and the codec to already be enabled for the library, and fails if the original source file is missing or the codec was already processed.

## Signature

```php
public function addOutputCodec(string $videoId, OutputCodec $outputCodecId): Video
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$outputCodecId` | `OutputCodec` | yes | The output codec to encode and add, must already be enabled for the library. |

## Returns

`Video`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Stream\Enum\OutputCodec;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->addOutputCodec(videoId: '00000000-0000-0000-0000-000000000000', outputCodecId: OutputCodec::X264);
```
