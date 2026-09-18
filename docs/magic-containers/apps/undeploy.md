# `$bunny->magicContainers->apps->undeploy()`

> Magic Containers API · `POST /apps/{appId}/undeploy`

Undeploy Application

Undeploys an application, stopping all running instances.

## Signature

```php
public function undeploy(string $appId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->apps->undeploy(appId: '00000000-0000-0000-0000-000000000000');
```
