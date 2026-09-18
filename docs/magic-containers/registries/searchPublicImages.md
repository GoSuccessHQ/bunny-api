# `$bunny->magicContainers->registries->searchPublicImages()`

> Magic Containers API · `POST /registries/public-images/search`

Search Public Container Images

Searches for public container images in a registry by prefix.

Docker Hub answers with its first 10 matches, whatever size and page (from 1) say.

## Signature

```php
public function searchPublicImages(
    string $registryId,
    string $prefix,
    ?int $size = null,
    ?int $page = null,
): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$registryId` | `string` | yes | The registry identifier. Can be "dockerhub", "github", or a private registry ID. |
| `$prefix` | `string` | yes |  |
| `$size` | `int\|null` | no |  |
| `$page` | `int\|null` | no |  |

## Returns

`list<ContainerImage>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->searchPublicImages(registryId: '00000000-0000-0000-0000-000000000000', prefix: 'example');
```
