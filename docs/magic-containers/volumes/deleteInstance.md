# `$bunny->magicContainers->volumes->deleteInstance()`

> Magic Containers API · `DELETE /apps/{appId}/volumes/{volumeId}/instances/{instanceId}`

Delete Volume Instance

Deletes a specific volume instance. The volume must be detached before deletion.

## Signature

```php
public function deleteInstance(string $appId, string $volumeId, string $instanceId): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$volumeId` | `string` | yes |  |
| `$instanceId` | `string` | yes |  |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->volumes->deleteInstance(appId: '00000000-0000-0000-0000-000000000000', volumeId: '00000000-0000-0000-0000-000000000000', instanceId: '00000000-0000-0000-0000-000000000000');
```
