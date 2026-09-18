# `$bunny->magicContainers->apps->setRegionSettings()`

> Magic Containers API · `PUT /apps/{appId}/region-settings`

Update Application Region Settings

Updates the region settings for an application, including allowed regions, required regions, and maximum allowed regions.

## Signature

```php
public function setRegionSettings(string $appId, UpdateRegionSettingsRequest $settings): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$settings` | `UpdateRegionSettingsRequest` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\UpdateRegionSettingsRequest;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->apps->setRegionSettings(appId: '00000000-0000-0000-0000-000000000000', settings: new UpdateRegionSettingsRequest(/* ... */));
```
