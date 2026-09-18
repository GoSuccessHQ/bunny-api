# `$bunny->core->storageZones->update()`

> Core Platform API · `POST /storagezone/{id}`

Update Storage Zone

## Signature

```php
public function update(int $id, StorageZoneUpdate $changes): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the storage zone that should be updated |
| `$changes` | `StorageZoneUpdate` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\StorageZoneUpdate;

$bunny = new Bunny('your-api-key');

$bunny->core->storageZones->update(id: 123, changes: new StorageZoneUpdate(/* ... */));
```
