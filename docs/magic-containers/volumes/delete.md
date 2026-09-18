# `$bunny->magicContainers->volumes->delete()`

> Magic Containers API · `DELETE /apps/{appId}/volumes/{volumeId}`

Delete All Volume Instances

Deletes all volume instances for a volume template. All instances must be detached before deletion.

## Signature

```php
public function delete(string $appId, string $volumeId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$volumeId` | `string` | yes |  |

## Returns

`list<string>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->volumes->delete(appId: '00000000-0000-0000-0000-000000000000', volumeId: '00000000-0000-0000-0000-000000000000');
```
