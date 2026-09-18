# `$bunny->shield->customPages->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`

Get the HTML of a custom page.

## Signature

```php
public function get(int $shieldZoneId, CustomPageType $type): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$type` | `CustomPageType` | yes | The page. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\CustomPageType;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->customPages->get(shieldZoneId: 123, type: CustomPageType::Block);
```
