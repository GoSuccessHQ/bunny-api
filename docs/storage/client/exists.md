# `$storage->exists()`

> Edge Storage API

Whether a file or directory exists.

`DESCRIBE /{storageZoneName}/{path}`

## Signature

```php
public function exists(string $path): bool
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file or directory, relative to the zone root. |

## Returns

`bool`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$result = $storage->exists(path: 'example');
```
