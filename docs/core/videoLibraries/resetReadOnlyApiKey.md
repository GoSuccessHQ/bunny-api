# `videoLibraries->resetReadOnlyApiKey()`

> Core Platform API · `POST /videolibrary/{id}/resetReadOnlyApiKey`

Reset Read Only API Key

## Signature

```php
public function resetReadOnlyApiKey(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the video library that should have the read only API key reset |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->resetReadOnlyApiKey(id: 123);
```
