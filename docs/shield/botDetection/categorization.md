# `$bunny->shield->botDetection->categorization()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/bot-categorization`

List bots available for explicit allow/block configuration on this Shield Zone, grouped by category.

## Signature

```php
public function categorization(int $shieldZoneId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |

## Returns

`list<BotCategorizationGroup>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->botDetection->categorization(shieldZoneId: 123);
```
