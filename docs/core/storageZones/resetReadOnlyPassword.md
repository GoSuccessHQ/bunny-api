# `$bunny->core->storageZones->resetReadOnlyPassword()`

> Core Platform API · `POST /storagezone/resetReadOnlyPassword`

Reset Read-Only Password

## Signature

```php
public function resetReadOnlyPassword(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the storage zone that should have the read-only password reset |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->storageZones->resetReadOnlyPassword(id: 123);
```
