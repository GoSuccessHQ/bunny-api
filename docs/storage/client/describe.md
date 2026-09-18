# `$storage->describe()`

> Edge Storage API

Get the metadata of a file or directory without downloading it.

Uses the `DESCRIBE` method, which the specification does not document but bunny.net's own CLI uses.

`DESCRIBE /{storageZoneName}/{path}`

## Signature

```php
public function describe(string $path): StorageObject
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file or directory, relative to the zone root. |

## Returns

`StorageObject`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$result = $storage->describe(path: 'example');
```
