# `$stream->videos->playHeatmap()`

> Stream API · `GET /library/{libraryId}/videos/{videoId}/play/heatmap`

Get the raw heatmap data the player shows on its timeline.

The format is not documented; an empty string means the library has heatmaps disabled or there is no data yet.

## Signature

```php
public function playHeatmap(string $videoId, ?string $token = null, ?int $expires = null): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$token` | `string\|null` | no | The embed view token, if token authentication is enabled. |
| `$expires` | `int\|null` | no | The expiry of the token as Unix timestamp. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->playHeatmap(videoId: '00000000-0000-0000-0000-000000000000');
```
