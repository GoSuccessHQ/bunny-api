# `$bunny->shield->botDetection->setBotAction()`

> Shield API · `PUT /shield/shield-zone/{shieldZoneId}/bot-categorization/bots/{botId}`

Set or clear the action applied to a categorised bot for this Shield Zone.

## Signature

```php
public function setBotAction(int $shieldZoneId, int $botId, BotCategorizationAction $action): BotCategorization
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$botId` | `int` | yes |  |
| `$action` | `BotCategorizationAction` | yes | 0 = None 1 = Block 2 = Allow 3 = Ignore |

## Returns

`BotCategorization`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\BotCategorizationAction;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->botDetection->setBotAction(shieldZoneId: 123, botId: 123, action: BotCategorizationAction::None);
```
