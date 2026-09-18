# `$bunny->magicContainers->regions->all()`

> Magic Containers API · `GET /regions`

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

`Paginator<Region>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->magicContainers->regions->all() as $item) {
    // ...
}
```
