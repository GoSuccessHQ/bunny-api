# `$bunny->magicContainers->volumes->update()`

> Magic Containers API · `PATCH /apps/{appId}/volumes/{volumeId}`

Update Volume

Partially updates a volume template's configuration including name and size. Only provided fields will be updated.

## Signature

```php
public function update(string $appId, string $volumeId, PatchVolumeRequest $changes): UpdatedVolume
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$volumeId` | `string` | yes |  |
| `$changes` | `PatchVolumeRequest` | yes |  |

## Returns

`UpdatedVolume`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\PatchVolumeRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->volumes->update(appId: '00000000-0000-0000-0000-000000000000', volumeId: '00000000-0000-0000-0000-000000000000', changes: new PatchVolumeRequest(/* ... */));
```
