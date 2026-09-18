# `$bunny->magicContainers->apps->regionSettings()`

> Magic Containers API · `GET /apps/{appId}/region-settings`

Get Application Region Settings

Retrieves the current region settings for an application, including allowed regions, required regions, and maximum allowed regions.

## Signature

```php
public function regionSettings(string $appId): RegionSettings
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |

## Returns

`RegionSettings`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->regionSettings(appId: '00000000-0000-0000-0000-000000000000');
```
