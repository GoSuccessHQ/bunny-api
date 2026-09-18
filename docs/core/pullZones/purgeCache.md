# `pullZones->purgeCache()`

> Core Platform API · `POST /pullzone/{id}/purgeCache`

Purge Cache

## Signature

```php
public function purgeCache(int $id, ?string $cacheTag = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Pull Zone that should be cleared |
| `$cacheTag` | `string\|null` | no |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->purgeCache(id: 123);
```
