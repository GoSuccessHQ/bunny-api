# `storageZones->regions()`

> Core Platform API · `GET /storagezone/regions`

Get Storage Zone Regions

## Signature

```php
public function regions(): array
```

## Returns

`list<StorageRegion>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->regions();
```
