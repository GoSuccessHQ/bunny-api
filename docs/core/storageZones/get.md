# `$bunny->core->storageZones->get()`

> Core Platform API · `GET /storagezone/{id}`

Get Storage Zone

## Signature

```php
public function get(int $id): StorageZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Storage Zone that should be returned |

## Returns

`StorageZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->get(id: 123);
```
