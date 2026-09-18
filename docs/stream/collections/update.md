# `$stream->collections->update()`

> Stream API · `POST /library/{libraryId}/collections/{collectionId}`

Update Collection

## Signature

```php
public function update(string $collectionId, ?string $name = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$collectionId` | `string` | yes | The unique ID of the collection. |
| `$name` | `string\|null` | no | The name to assign to the collection. Required, up to 255 characters. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->collections->update(collectionId: '00000000-0000-0000-0000-000000000000');
```
