# `videoLibraries->get()`

> Core Platform API · `GET /videolibrary/{id}`

Get Video Library

## Signature

```php
public function get(int $id): VideoLibrary
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Video Library that will be returned |

## Returns

`VideoLibrary`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->get(id: 123);
```
