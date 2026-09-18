# `$bunny->magicContainers->volumes->list()`

> Magic Containers API · `GET /apps/{appId}/volumes`

List Volumes

Lists all volume templates and their instances for an application, including usage statistics.

## Signature

```php
public function list(string $appId): VolumeList
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`VolumeList`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->volumes->list(appId: '00000000-0000-0000-0000-000000000000');
```
