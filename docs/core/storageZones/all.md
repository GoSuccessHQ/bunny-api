# `$bunny->core->storageZones->all()`

> Core Platform API · `GET /storagezone`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    ?bool $includeDeleted = null,
    ?string $search = null,
    int $perPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$includeDeleted` | `bool\|null` | no |  |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<StorageZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->storageZones->all() as $item) {
    // ...
}
```
