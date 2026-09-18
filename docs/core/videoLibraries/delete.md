# `videoLibraries->delete()`

> Core Platform API · `DELETE /videolibrary/{id}`

Delete Video Library

## Signature

```php
public function delete(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Video Library that should be deleted |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->delete(id: 123);
```
