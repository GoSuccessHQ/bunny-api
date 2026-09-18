# `$bunny->edgeScripting->releases->list()`

> Edge Scripting API · `GET /compute/script/{id}/releases`

Get Releases

## Signature

```php
public function list(int $scriptId, int $page = 1, int $perPage = 100): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script for which published releases would be returned |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Page<EdgeScriptRelease>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->releases->list(scriptId: 123);
```
