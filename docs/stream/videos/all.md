# `$stream->videos->all()`

> Stream API · `GET /library/{libraryId}/videos`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    ?string $search = null,
    ?string $collection = null,
    ?string $orderBy = null,
    int $itemsPerPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no | Filters videos by title, case-insensitive substring match. |
| `$collection` | `string\|null` | no | Filters videos by collection ID. |
| `$orderBy` | `string\|null` | no | The field to order results by. Possible values: date, title — any other value falls back to date. |
| `$itemsPerPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<Video>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

foreach ($stream->videos->all() as $item) {
    // ...
}
```
