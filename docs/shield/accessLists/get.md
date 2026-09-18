# `$bunny->shield->accessLists->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/access-lists/{id}`

Get the specified Custom Access List associated with a Shield Zone

## Signature

```php
public function get(int $shieldZoneId, int $id): CustomAccessList
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Custom Access List belongs. |
| `$id` | `int` | yes | The ID of the Custom Access List to retrieve. |

## Returns

`CustomAccessList`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->get(shieldZoneId: 123, id: 123);
```
