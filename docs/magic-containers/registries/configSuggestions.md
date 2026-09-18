# `$bunny->magicContainers->registries->configSuggestions()`

> Magic Containers API · `POST /registries/config-suggestions`

Get Container Config Suggestions

Gets recommended configuration for a container image including endpoint configurations and environment variables.

## Signature

```php
public function configSuggestions(
    string $registryId,
    string $imageName,
    string $imageNamespace,
    string $tag,
): ContainerConfigSuggestions
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |
| `$imageName` | `string` | yes |  |
| `$imageNamespace` | `string` | yes |  |
| `$tag` | `string` | yes |  |

## Returns

`ContainerConfigSuggestions`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->configSuggestions(registryId: '00000000-0000-0000-0000-000000000000', imageName: 'example', imageNamespace: 'example', tag: 'example');
```
