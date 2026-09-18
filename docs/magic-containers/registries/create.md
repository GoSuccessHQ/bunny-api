# `$bunny->magicContainers->registries->create()`

> Magic Containers API · `POST /registries`

Add container registry

Add a container registry for user.

## Signature

```php
public function create(ContainerRegistryRequest $registry): SaveContainerRegistryResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registry` | `ContainerRegistryRequest` | yes |  |

## Returns

`SaveContainerRegistryResult`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\ContainerRegistryRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->create(registry: new ContainerRegistryRequest(/* ... */));
```
