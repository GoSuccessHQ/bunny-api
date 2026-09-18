# `$bunny->core->edgeRules->addOrUpdate()`

> Core Platform API · `POST /pullzone/{pullZoneId}/edgerules/addOrUpdate`

Add/Update Edge Rule

## Signature

```php
public function addOrUpdate(int $pullZoneId, EdgeRule $edgeRule): EdgeRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the Pull Zone where the Edge Rule will be created |
| `$edgeRule` | `EdgeRule` | yes |  |

## Returns

`EdgeRule`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\EdgeRule;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->edgeRules->addOrUpdate(pullZoneId: 123, edgeRule: new EdgeRule(/* ... */));
```
