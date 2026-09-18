# `$storage->delete()`

> Edge Storage API · `DELETE /{storageZoneName}/{path}/{fileName}`

Delete a file, or a directory with everything in it when the path ends with a slash.

## Signature

```php
public function delete(string $path): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file or directory, relative to the zone root. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$storage->delete(path: 'example');
```
