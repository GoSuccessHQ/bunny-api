# `$bunny->magicContainers->registries->get()`

> Magic Containers API · `GET /registries/{registryId}`

Get Container Registry

Retrieves a specific container registry by its ID.

Only the registries of the account; the global public registries that list() includes answer 404.

## Signature

```php
public function get(int $registryId): ContainerRegistry
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `int` | yes |  |

## Returns

`ContainerRegistry`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->get(registryId: 123);
```
