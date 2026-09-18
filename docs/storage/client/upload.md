# `$storage->upload()`

> Edge Storage API · `PUT /{storageZoneName}/{path}/{fileName}`

Upload a file. Missing directories are created; an existing file is replaced.

## Signature

```php
public function upload(
    string $path,
    Stream|string $contents,
    ?string $contentType = null,
    bool $verifyChecksum = true,
): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$path` | `string` | yes | The file, relative to the zone root. |
| `$contents` | `string\|Stream` | yes | The file contents, e.g. `Stream::fromFile('video.mp4')`. |
| `$contentType` | `string\|null` | no | The content type to serve the file with. By default the CDN derives it from the file extension. |
| `$verifyChecksum` | `bool` | no | Send the SHA-256 checksum, so bunny.net rejects a corrupted upload (HTTP 400). Reads the contents one extra time; a non-seekable stream is buffered in a temporary file first. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Storage\StorageRegion;

$bunny = new Bunny('your-api-key');
$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::Falkenstein);

$storage->upload(path: 'example', contents: Stream::fromFile('path/to/file'));
```
