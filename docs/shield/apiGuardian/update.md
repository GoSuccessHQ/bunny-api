# `$bunny->shield->apiGuardian->update()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/api-guardian`

Update the API Guardian configuration (enabled, execution mode, body limit action)

## Signature

```php
public function update(
    int $shieldZoneId,
    ?bool $isEnabled = null,
    ?WafExecutionMode $executionMode = null,
    ?WafPayloadLimitAction $bodyLimitAction = null,
    ?UnmatchedPathAction $unmatchedPathAction = null,
): ApiGuardianConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$isEnabled` | `bool\|null` | no | Enable or disable API Guardian entirely for this shield zone. |
| `$executionMode` | `WafExecutionMode\|null` | no | 0 = Log 1 = Block |
| `$bodyLimitAction` | `WafPayloadLimitAction\|null` | no | 0 = Block 1 = Log 2 = Ignore |
| `$unmatchedPathAction` | `UnmatchedPathAction\|null` | no | 0 = Block 1 = Log 2 = Ignore |

## Returns

`ApiGuardianConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->apiGuardian->update(shieldZoneId: 123);
```
