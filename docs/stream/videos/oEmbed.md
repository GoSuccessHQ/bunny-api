# `$stream->videos->oEmbed()`

> Stream API · `GET /OEmbed`

Get oEmbed data

Implements the oEmbed specification (oembed.com) for bunny.net video embed URLs, allowing third-party sites and tools to auto-generate an embeddable player for a pasted video link. url must be a bunny.net player URL for an existing video — either the legacy player (e.g. "https://iframe.mediadelivery.net/embed/{libraryId}/{videoId}") or the new player (e.g. "https://player.mediadelivery.net/embed/{libraryId}/{videoId}") — not an arbitrary link. Only the library ID and video ID segments are read, so /play/ URLs are accepted as well as /embed/ ones. For private libraries, the request must satisfy the library's referer restrictions and, separately, a valid token and expires pair if token authentication is enabled — one does not substitute for the other.

## Signature

```php
public function oEmbed(
    string $url,
    ?int $maxWidth = null,
    ?int $maxHeight = null,
    ?string $token = null,
    ?int $expires = null,
): VideoOEmbed
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$url` | `string` | yes | The bunny.net player URL of the video to generate oEmbed data for (legacy iframe.mediadelivery.net or new player.mediadelivery.net player). Both /embed/ and /play/ paths are accepted. |
| `$maxWidth` | `int\|null` | no | Maximum width in pixels for the embedded player; the player is scaled down to fit while preserving aspect ratio. |
| `$maxHeight` | `int\|null` | no | Maximum height in pixels for the embedded player; the player is scaled down to fit while preserving aspect ratio. |
| `$token` | `string\|null` | no | Signed access token, required when the video library has token authentication enabled. An allowed referer is checked separately and does not exempt the request from this requirement. |
| `$expires` | `int\|null` | no | Unix timestamp (seconds) the token is valid until, required when the video library has token authentication enabled. |

## Returns

`VideoOEmbed`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->oEmbed(url: 'https://example.com/');
```
