# `$bunny->edgeScripting->scripts->create()`

> Edge Scripting API · `POST /compute/script`

Add Edge Script

## Signature

```php
public function create(
    string $name,
    EdgeScriptType $scriptType,
    ?string $code = null,
    ?bool $createLinkedPullZone = null,
    ?string $linkedPullZoneName = null,
    ?SourceCodeIntegration $integration = null,
): EdgeScript
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$name` | `string` | yes |  |
| `$scriptType` | `EdgeScriptType` | yes | 0 = DNS 1 = CDN 2 = Middleware |
| `$code` | `string\|null` | no |  |
| `$createLinkedPullZone` | `bool\|null` | no |  |
| `$linkedPullZoneName` | `string\|null` | no |  |
| `$integration` | `SourceCodeIntegration\|null` | no |  |

## Returns

`EdgeScript`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\EdgeScripting\Enum\EdgeScriptType;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->create(name: 'example', scriptType: EdgeScriptType::DNS);
```
