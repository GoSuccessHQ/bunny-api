# `$bunny->magicContainers->containers->get()`

> Magic Containers API · `GET /apps/{appId}/containers/{containerId}`

Get Container Template

Gets a container template within an application.

## Signature

```php
public function get(string $appId, string $containerId): ContainerTemplate
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$containerId` | `string` | yes | The ID of the container template |

## Returns

`ContainerTemplate`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->containers->get(appId: '00000000-0000-0000-0000-000000000000', containerId: '00000000-0000-0000-0000-000000000000');
```
