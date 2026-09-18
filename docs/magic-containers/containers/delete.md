# `$bunny->magicContainers->containers->delete()`

> Magic Containers API · `DELETE /apps/{appId}/containers/{containerId}`

Delete Container Template

Deletes a container template from an application.

## Signature

```php
public function delete(string $appId, string $containerId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$containerId` | `string` | yes | The ID of the container template to delete |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->containers->delete(appId: '00000000-0000-0000-0000-000000000000', containerId: '00000000-0000-0000-0000-000000000000');
```
