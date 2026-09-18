# `$stream->collections->create()`

> Stream API · `POST /library/{libraryId}/collections`

Create Collection

## Signature

```php
public function create(?string $name = null): Collection
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$name` | `string\|null` | no | The name to assign to the collection. Required, up to 255 characters. |

## Returns

`Collection`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->collections->create();
```
