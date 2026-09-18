# `$bunny->shield->accessLists->configure()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/access-lists/configurations/{id}`

Update Access List Configuration for a Shield Zone

Takes the configuration ID that list() reports as configurationId for every managed and custom list; it differs from the list ID that create() and get() return.

## Signature

```php
public function configure(
    int $shieldZoneId,
    int $id,
    ?bool $isEnabled = null,
    ?AccessListAction $action = null,
): AccessListConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Access List Configuration belongs. |
| `$id` | `int` | yes | The ID of the Access List Configuration to update. |
| `$isEnabled` | `bool\|null` | no | Whether the access list should be enabled or disabled. If null, the current state is preserved. |
| `$action` | `AccessListAction\|null` | no | 0 = None 1 = Allow 2 = Block 3 = Challenge 4 = Log 5 = Bypass |

## Returns

`AccessListConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->configure(shieldZoneId: 123, id: 123);
```
