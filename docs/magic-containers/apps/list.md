# `$bunny->magicContainers->apps->list()`

> Magic Containers API · `GET /apps`

List Applications

Lists all applications for the authenticated user with their current status.

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

`Page<ApplicationListItem>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->list();
```
