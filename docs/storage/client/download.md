# `$storage->download()`

> Edge Storage API · `GET /{storageZoneName}/{path}/{fileName}`

Download a file into a stream, e.g. `Stream::fromFile('backup.zip', 'wb')`.

The file is written from the stream's current position. If the transfer fails half-way and the stream is seekable, it is truncated before the retry, so the target never holds a mix of two attempts.

## Signature

```php
public function download(string $path, Stream $target): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file, relative to the zone root. |
| `$target` | `Stream` | yes | Where to write the file. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$storage->download(path: 'example', target: Stream::fromFile('path/to/file'));
```
