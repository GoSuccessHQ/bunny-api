# `$stream->videos->playData()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}/play`

Get Video play data

Returns playback URLs and metadata for the video. For libraries with token authentication enabled, token and expires must be a valid signed pair or the request is rejected.

## Signature

```php
public function playData(string $videoId, ?string $token = null, ?int $expires = null): VideoPlayData
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$token` | `string\|null` | no | Signed access token, required when the library has token authentication enabled. |
| `$expires` | `int\|null` | no | Unix timestamp (seconds) the token is valid until, required when the library has token authentication enabled. |

## Returns

`VideoPlayData`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->playData(videoId: '00000000-0000-0000-0000-000000000000');
```
