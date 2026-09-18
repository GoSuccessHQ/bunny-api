# `$bunny->magicContainers->apps->setAutoscaling()`

> Magic Containers API · `PUT /apps/{appId}/autoscaling`

Update Application Autoscaling

Updates the autoscaling settings for an application, including minimum and maximum replica counts.

## Signature

```php
public function setAutoscaling(string $appId, AutoscalingSettings $settings): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$settings` | `AutoscalingSettings` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\AutoscalingSettings;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->apps->setAutoscaling(appId: '00000000-0000-0000-0000-000000000000', settings: new AutoscalingSettings(/* ... */));
```
