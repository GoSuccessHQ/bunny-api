# `$bunny->magicContainers->pods->recreate()`

> Magic Containers API · `POST /apps/{appId}/pods/{podId}/recreate`

Recreate Pod

Recreate a pod, deleting previous one.

## Signature

```php
public function recreate(string $appId, string $podId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |
| `$podId` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->pods->recreate(appId: '00000000-0000-0000-0000-000000000000', podId: '00000000-0000-0000-0000-000000000000');
```
