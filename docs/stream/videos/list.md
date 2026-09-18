# `$stream->videos->list()`

> Stream API · `GET /library/{libraryId}/videos`

List Videos

## Signature

```php
public function list(
    int $page = 1,
    int $itemsPerPage = 100,
    ?string $search = null,
    ?string $collection = null,
    ?string $orderBy = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$itemsPerPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | Filters videos by title, case-insensitive substring match. |
| `$collection` | `string\|null` | no | Filters videos by collection ID. |
| `$orderBy` | `string\|null` | no | The field to order results by. Possible values: date, title — any other value falls back to date. |

## Returns

`Page<Video>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->list();
```
