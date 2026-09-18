# `videoLibraries->all()`

> Core Platform API · `GET /videolibrary`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(?string $search = null, int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<VideoLibrary>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->videoLibraries->all() as $item) {
    // ...
}
```
