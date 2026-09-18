# `$bunny->shield->botDetection->setCategoryAction()`

> Shield API · `PUT /shield/shield-zone/{shieldZoneId}/bot-categorization/categories/{category}`

Set or clear the action applied to every bot in a category for this Shield Zone.

## Signature

```php
public function setCategoryAction(int $shieldZoneId, BotCategory $category, BotCategoryAction $action): BotCategorySetting
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$category` | `BotCategory` | yes | 0 = None 1 = SEO 2 = AIScraper 3 = AITool 4 = Tool 5 = Ads 6 = Preview 7 = Social 255 = System |
| `$action` | `BotCategoryAction` | yes | 0 = None 1 = Block 2 = Allow |

## Returns

`BotCategorySetting`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\BotCategory;
use GoSuccess\Bunny\Shield\Enum\BotCategoryAction;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->botDetection->setCategoryAction(shieldZoneId: 123, category: BotCategory::None, action: BotCategoryAction::None);
```
