# `$bunny->magicContainers->regions->list()`

> Magic Containers API · `GET /regions`

List Regions

Lists all available regions where applications can be deployed, including their anycast support and capacity status.

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

`Page<Region>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->regions->list();
```
