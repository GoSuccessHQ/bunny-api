# `$storage->get()`

> Edge Storage API · `GET /{storageZoneName}/{path}/{fileName}`

Download a file into memory. For large files, use download().

## Signature

```php
public function get(string $path): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file, relative to the zone root. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$result = $storage->get(path: 'example');
```
