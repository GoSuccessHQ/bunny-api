# `$bunny->magicContainers->containers->patch()`

> Magic Containers API · `PATCH /apps/{appId}/containers/{containerId}`

Patch Container Template

Partially updates a container template within an application. Only provided fields will be updated; existing fields not included in the request will remain unchanged.

## Signature

```php
public function patch(string $appId, string $containerId, PatchContainerRequest $changes): ContainerTemplate
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$containerId` | `string` | yes | The ID of the container template to update |
| `$changes` | `PatchContainerRequest` | yes |  |

## Returns

`ContainerTemplate`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\PatchContainerRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->containers->patch(appId: '00000000-0000-0000-0000-000000000000', containerId: '00000000-0000-0000-0000-000000000000', changes: new PatchContainerRequest(/* ... */));
```
