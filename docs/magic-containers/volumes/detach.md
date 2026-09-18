# `$bunny->magicContainers->volumes->detach()`

> Magic Containers API · `POST /apps/{appId}/volumes/{volumeId}/detach`

Detach Volume

Detaches a volume template from all application containers.

## Signature

```php
public function detach(string $appId, string $volumeId): DetachedVolume
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$volumeId` | `string` | yes |  |

## Returns

`DetachedVolume`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->volumes->detach(appId: '00000000-0000-0000-0000-000000000000', volumeId: '00000000-0000-0000-0000-000000000000');
```
