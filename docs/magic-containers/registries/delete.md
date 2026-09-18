# `$bunny->magicContainers->registries->delete()`

> Magic Containers API · `DELETE /registries/{registryId}`

Delete Container Registry

Deletes a container registry. Returns an error if the registry is currently in use by any applications.

## Signature

```php
public function delete(int $registryId): RemoveContainerRegistryResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `int` | yes |  |

## Returns

`RemoveContainerRegistryResult`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->delete(registryId: 123);
```
