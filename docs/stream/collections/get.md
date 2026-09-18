# `$stream->collections->get()`

> Stream API · `GET /library/{libraryId}/collections/{collectionId}`

Get Collection

Returns the details of a collection. Collections can group both videos and live streams, reflected separately in VideoCount and LiveStreamCount; PreviewImageUrls only reflects the videos in the collection.

## Signature

```php
public function get(string $collectionId, ?bool $includeThumbnails = null): Collection
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$collectionId` | `string` | yes | The unique ID of the collection. |
| `$includeThumbnails` | `bool\|null` | no | If set to true, populates PreviewImageUrls with thumbnails for videos in the collection. |

## Returns

`Collection`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->collections->get(collectionId: '00000000-0000-0000-0000-000000000000');
```
