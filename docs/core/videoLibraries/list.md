# `$bunny->core->videoLibraries->list()`

> Core Platform API · `GET /videolibrary`

List Video Libraries

## Signature

```php
public function list(int $page = 1, int $perPage = 100, ?string $search = null): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |

## Returns

`Page<VideoLibrary>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->list();
```
