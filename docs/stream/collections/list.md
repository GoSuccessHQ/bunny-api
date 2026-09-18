# `$stream->collections->list()`

> Stream API · `GET /library/{libraryId}/collections`

Get Collection List

Returns a paginated list of collections for the library. Collections can group both videos and live streams, reflected separately in VideoCount and LiveStreamCount; preview thumbnails only reflect the videos in each collection. When includeThumbnails is true, preview thumbnails are only populated for the first 60 collections of the returned page — later items in a larger page are returned without thumbnails.

## Signature

```php
public function list(
    int $page = 1,
    int $itemsPerPage = 100,
    ?string $search = null,
    ?string $orderBy = null,
    ?bool $includeThumbnails = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$itemsPerPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | Filters collections by name, case-insensitive substring match. |
| `$orderBy` | `string\|null` | no | The field to order results by. Possible values: date, title — any other value falls back to date. |
| `$includeThumbnails` | `bool\|null` | no | If set to true, populates PreviewImageUrls for each collection (only for the first 60 collections of the page). |

## Returns

`Page<Collection>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->collections->list();
```
