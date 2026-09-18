# `$bunny->magicContainers->registries->digest()`

> Magic Containers API · `POST /registries/digest`

Get Container Image Digest

Retrieves the digest information for a specific container image tag.

## Signature

```php
public function digest(
    string $registryId,
    string $imageName,
    string $imageNamespace,
    string $tag,
): ImageTagInfo
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |
| `$imageName` | `string` | yes |  |
| `$imageNamespace` | `string` | yes |  |
| `$tag` | `string` | yes |  |

## Returns

`ImageTagInfo`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->digest(registryId: '00000000-0000-0000-0000-000000000000', imageName: 'example', imageNamespace: 'example', tag: 'example');
```
