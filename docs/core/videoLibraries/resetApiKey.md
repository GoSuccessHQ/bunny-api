# `videoLibraries->resetApiKey()`

> Core Platform API · `POST /videolibrary/{id}/resetApiKey`

Reset API Key

## Signature

```php
public function resetApiKey(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the video library that should have the API key reset |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->resetApiKey(id: 123);
```
