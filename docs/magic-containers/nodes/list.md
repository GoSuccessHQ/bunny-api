# `$bunny->magicContainers->nodes->list()`

> Magic Containers API · `GET /nodes`

List Nodes

Lists all node IP addresses in the Magic Containers network.

## Signature

```php
public function list(?string $nextCursor = null, int $limit = 100): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$nextCursor` | `string\|null` | no | The position returned by the previous page; null for the first page. |
| `$limit` | `int` | no | The number of items per page. |

## Returns

`Page<string>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->nodes->list();
```
