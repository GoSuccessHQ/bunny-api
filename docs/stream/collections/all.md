# `$stream->collections->all()`

> Stream API · `GET /library/{libraryId}/collections`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    ?string $search = null,
    ?string $orderBy = null,
    ?bool $includeThumbnails = null,
    int $itemsPerPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no | Filters collections by name, case-insensitive substring match. |
| `$orderBy` | `string\|null` | no | The field to order results by. Possible values: date, title — any other value falls back to date. |
| `$includeThumbnails` | `bool\|null` | no | If set to true, populates PreviewImageUrls for each collection (only for the first 60 collections of the page). |
| `$itemsPerPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<Collection>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

foreach ($stream->collections->all() as $item) {
    // ...
}
```
