# `$storage->deleteDirectory()`

> Edge Storage API · `DELETE /{storageZoneName}/{path}/`

Delete a directory with everything in it.

## Signature

```php
public function deleteDirectory(string $path, bool $allowRoot = false): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The directory, relative to the zone root. |
| `$allowRoot` | `bool` | no | Set to true to delete the root, i.e. every file of the zone. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$storage->deleteDirectory(path: 'example');
```
