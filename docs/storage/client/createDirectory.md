# `$storage->createDirectory()`

> Edge Storage API · `PUT /{storageZoneName}/{path}/`

Create a directory, including missing parents.

## Signature

```php
public function createDirectory(string $path): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The directory, relative to the zone root. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$storage->createDirectory(path: 'example');
```
