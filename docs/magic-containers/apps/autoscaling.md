# `$bunny->magicContainers->apps->autoscaling()`

> Magic Containers API · `GET /apps/{appId}/autoscaling`

Get Application Autoscaling

Retrieves the current autoscaling settings for an application, including minimum and maximum replica counts.

## Signature

```php
public function autoscaling(string $appId): AutoscalingSettings
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |

## Returns

`AutoscalingSettings`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->autoscaling(appId: '00000000-0000-0000-0000-000000000000');
```
