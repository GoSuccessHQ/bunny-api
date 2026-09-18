# `$bunny->magicContainers->registries->images()`

> Magic Containers API · `POST /registries/images`

List Container Images

Lists all container images available in a private registry.

## Signature

```php
public function images(string $registryId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |

## Returns

`list<ContainerImage>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->images(registryId: '00000000-0000-0000-0000-000000000000');
```
