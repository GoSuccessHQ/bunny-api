# `$bunny->core->storageZones->list()`

> Core Platform API · `GET /storagezone`

List Storage Zones

## Signature

```php
public function list(
    int $page = 1,
    int $perPage = 100,
    ?bool $includeDeleted = null,
    ?string $search = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$includeDeleted` | `bool\|null` | no |  |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |

## Returns

`Page<StorageZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->list();
```
