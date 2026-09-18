# `$bunny->magicContainers->apps->restart()`

> Magic Containers API · `POST /apps/{appId}/restart`

Restart Application

Triggers a restart of all pods for the specified application.

## Signature

```php
public function restart(string $appId): void
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

$bunny->magicContainers->apps->restart(appId: '00000000-0000-0000-0000-000000000000');
```
