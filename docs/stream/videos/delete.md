# `$stream->videos->delete()`

> Stream API · `DELETE /library/{libraryId}/videos/{videoId}`

Delete Video

Deletes the video. This cannot be undone. The video stops appearing in lookups right away, but playback may still be possible for some time afterward before access is fully revoked.

## Signature

```php
public function delete(string $videoId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->delete(videoId: '00000000-0000-0000-0000-000000000000');
```
