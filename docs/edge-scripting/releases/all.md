# `$bunny->edgeScripting->releases->all()`

> Edge Scripting API · `GET /compute/script/{id}/releases`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(int $scriptId, int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script for which published releases would be returned |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<EdgeScriptRelease>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->edgeScripting->releases->all(scriptId: 123) as $item) {
    // ...
}
```
