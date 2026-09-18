# `$bunny->magicContainers->registries->tags()`

> Magic Containers API · `POST /registries/tags`

List Container Image Tags

Lists all available tags for a specific container image.

## Signature

```php
public function tags(string $registryId, string $imageName, string $imageNamespace): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |
| `$imageName` | `string` | yes |  |
| `$imageNamespace` | `string` | yes |  |

## Returns

`list<ContainerImageTag>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->tags(registryId: '00000000-0000-0000-0000-000000000000', imageName: 'example', imageNamespace: 'example');
```
