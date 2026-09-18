# `edgeRules->delete()`

> Core Platform API · `DELETE /pullzone/{pullZoneId}/edgerules/{edgeRuleId}`

Delete Edge Rule

## Signature

```php
public function delete(int $pullZoneId, string $edgeRuleId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the Pull Zone that contains the Edge Rule |
| `$edgeRuleId` | `string` | yes | The ID of the Edge Rule that should be deleted |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->edgeRules->delete(pullZoneId: 123, edgeRuleId: '00000000-0000-0000-0000-000000000000');
```
