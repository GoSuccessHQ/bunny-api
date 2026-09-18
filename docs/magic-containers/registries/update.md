# `$bunny->magicContainers->registries->update()`

> Magic Containers API · `PUT /registries/{registryId}`

Update Container Registry

Updates an existing container registry configuration including credentials.

## Signature

```php
public function update(int $registryId, ContainerRegistryRequest $registry): SaveContainerRegistryResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `int` | yes |  |
| `$registry` | `ContainerRegistryRequest` | yes |  |

## Returns

`SaveContainerRegistryResult`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\ContainerRegistryRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->update(registryId: 123, registry: new ContainerRegistryRequest(/* ... */));
```
