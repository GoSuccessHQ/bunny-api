# `$bunny->magicContainers->registries->imageConfig()`

> Magic Containers API · `POST /registries/image-config`

Get Image Config

Retrieves endpoint and volume suggestions for a container image from a registry.

## Signature

```php
public function imageConfig(
    string $registryId,
    string $imageName,
    string $imageNamespace,
    string $tag,
): ImageConfig
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |
| `$imageName` | `string` | yes |  |
| `$imageNamespace` | `string` | yes |  |
| `$tag` | `string` | yes |  |

## Returns

`ImageConfig`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->imageConfig(registryId: '00000000-0000-0000-0000-000000000000', imageName: 'example', imageNamespace: 'example', tag: 'example');
```
