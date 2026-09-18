# `$bunny->shield->customPages->delete()`

> Shield API · `DELETE /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`

Delete a custom page; bunny.net's own page is shown again.

## Signature

```php
public function delete(int $shieldZoneId, CustomPageType $type): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$type` | `CustomPageType` | yes | The page. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\CustomPageType;

$bunny = new Bunny('your-api-key');

$bunny->shield->customPages->delete(shieldZoneId: 123, type: CustomPageType::Block);
```
