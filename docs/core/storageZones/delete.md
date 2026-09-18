# `$bunny->core->storageZones->delete()`

> Core Platform API · `DELETE /storagezone/{id}`

Delete Storage Zone

## Signature

```php
public function delete(int $id, ?bool $deleteLinkedPullZones = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The Storage Zone ID that should be deleted |
| `$deleteLinkedPullZones` | `bool\|null` | no | Deletes all pull zones linked to this storage zone (default behavior) |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->storageZones->delete(id: 123);
```
