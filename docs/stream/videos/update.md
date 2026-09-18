# `$stream->videos->update()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}`

Update Video

## Signature

```php
public function update(string $videoId, VideoUpdate $changes): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$changes` | `VideoUpdate` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Stream\Model\VideoUpdate;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->update(videoId: '00000000-0000-0000-0000-000000000000', changes: new VideoUpdate(/* ... */));
```
