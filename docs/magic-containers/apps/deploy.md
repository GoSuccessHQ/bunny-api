# `$bunny->magicContainers->apps->deploy()`

> Magic Containers API · `POST /apps/{appId}/deploy`

Deploy Application

Deploys an application, making it active and running.

## Signature

```php
public function deploy(string $appId): void
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

$bunny->magicContainers->apps->deploy(appId: '00000000-0000-0000-0000-000000000000');
```
