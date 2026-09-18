# `$bunny->shield->accessLists->delete()`

> Shield API · `DELETE /shield/shield-zone/{shieldZoneId}/access-lists/{id}`

Delete the specified Custom Access List associated with a Shield Zone

## Signature

```php
public function delete(int $shieldZoneId, int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Custom Access List belongs. |
| `$id` | `int` | yes | The ID of the Custom Access List to delete. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->shield->accessLists->delete(shieldZoneId: 123, id: 123);
```
