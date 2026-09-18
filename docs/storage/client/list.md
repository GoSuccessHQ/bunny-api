# `$storage->list()`

> Edge Storage API · `GET /{storageZoneName}/{path}/`

List the files and directories in a directory.

## Signature

```php
public function list(string $directory = ''): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$directory` | `string` | no | The directory, relative to the zone root; empty for the root. |

## Returns

`list<StorageObject>`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$result = $storage->list();
```
