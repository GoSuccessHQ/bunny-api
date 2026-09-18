# `$bunny->shield->apiGuardian->uploadSpecification()`

> Shield API · `POST /shield/shield-zone/{shieldZoneId}/api-guardian/spec`

Upload your OpenAPI specification

## Signature

```php
public function uploadSpecification(
    int $shieldZoneId,
    ?string $content = null,
    ?bool $enforceAuthorizationValidation = null,
): ApiGuardian
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$content` | `string\|null` | no | The file contents of the OpenAPI specification. |
| `$enforceAuthorizationValidation` | `bool\|null` | no | Whether to enforce authentication requirements for endpoints. On upload, defaults to true if not specified. On update, existing endpoint auth settings are preserved if not specified. |

## Returns

`ApiGuardian`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->apiGuardian->uploadSpecification(shieldZoneId: 123);
```
