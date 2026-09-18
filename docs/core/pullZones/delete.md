# `$bunny->core->pullZones->delete()`

> Core Platform API · `DELETE /pullzone/{id}`

Delete Pull Zone

## Signature

```php
public function delete(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Pull Zone that should be deleted |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->delete(id: 123);
```
