# `$stream->collections->delete()`

> Stream API · `DELETE /library/{libraryId}/collections/{collectionId}`

Delete Collection

Permanently deletes the collection together with every video and live stream it contains — nothing is moved out of the collection first, it is deleted along with it. Fails with 400 if the collection contains a currently running live stream; stop it before deleting the collection. This cannot be undone.

## Signature

```php
public function delete(string $collectionId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$collectionId` | `string` | yes | The unique ID of the collection. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->collections->delete(collectionId: '00000000-0000-0000-0000-000000000000');
```
