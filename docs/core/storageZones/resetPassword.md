# `$bunny->core->storageZones->resetPassword()`

> Core Platform API · `POST /storagezone/{id}/resetPassword`

Reset Password

## Signature

```php
public function resetPassword(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the storage zone that should have the password reset |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->storageZones->resetPassword(id: 123);
```
