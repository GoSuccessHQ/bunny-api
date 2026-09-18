# `storageZones->create()`

> Core Platform API · `POST /storagezone`

Add Storage Zone

## Signature

```php
public function create(StorageZoneCreate $storageZone): StorageZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$storageZone` | `StorageZoneCreate` | yes |  |

## Returns

`StorageZone`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\StorageZoneCreate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->create(storageZone: new StorageZoneCreate(/* ... */));
```
