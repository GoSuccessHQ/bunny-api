# `$bunny->magicContainers->apps->all()`

> Magic Containers API · `GET /apps`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(int $limit = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$limit` | `int` | no | The number of items per page. |

## Returns

`Paginator<ApplicationListItem>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->magicContainers->apps->all() as $item) {
    // ...
}
```
