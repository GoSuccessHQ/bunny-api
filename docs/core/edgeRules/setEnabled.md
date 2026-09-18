# `$bunny->core->edgeRules->setEnabled()`

> Core Platform API · `POST /pullzone/{pullZoneId}/edgerules/{edgeRuleId}/setEdgeRuleEnabled`

Enable or disable an edge rule.

The request body's `Id` must be the pull zone id. The specification does not say so; it is documented by other clients (e.g. simplesurance/bunny-go), so this method fills it in instead of asking for it.

## Signature

```php
public function setEnabled(int $pullZoneId, string $edgeRuleId, bool $enabled): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the pull zone that contains the edge rule |
| `$edgeRuleId` | `string` | yes | The GUID of the edge rule |
| `$enabled` | `bool` | yes | Whether the edge rule should be enabled |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->edgeRules->setEnabled(pullZoneId: 123, edgeRuleId: '00000000-0000-0000-0000-000000000000', enabled: true);
```
