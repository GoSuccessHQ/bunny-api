# `$bunny->shield->apiGuardian->updateEndpoint()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/api-guardian/endpoint/{endpointId}`

Update your API Guardian Endpoint configuration

## Signature

```php
public function updateEndpoint(
    int $shieldZoneId,
    int $endpointId,
    ?bool $isEnabled = null,
    ?bool $validateRequestBodySchema = null,
    ?bool $validateResponseBodySchema = null,
    ?bool $validateAuthorization = null,
    ?ApiGuardianParameters $injectionDetectionParameters = null,
    ?bool $detectParameterXss = null,
    ?bool $detectParameterSqli = null,
    ?bool $rateLimitingEnabled = null,
    ?ApiGuardianRateLimitType $rateLimitingType = null,
    ?int $rateLimitingRequestCount = null,
    ?RateLimitTimeframe $rateLimitingTimeframe = null,
): ApiGuardianEndpoint
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$endpointId` | `int` | yes |  |
| `$isEnabled` | `bool\|null` | no | Enable or disable this endpoint. Disabled endpoints are not enforced. |
| `$validateRequestBodySchema` | `bool\|null` | no | Enable or disable request body schema validation. Only effective if the endpoint's OAS operation defines a request body schema. |
| `$validateResponseBodySchema` | `bool\|null` | no | Enable or disable response body schema validation. Only effective if the endpoint's OAS operation defines response schemas. |
| `$validateAuthorization` | `bool\|null` | no | Enable or disable enforcement of authentication requirements as defined in the OAS security schemes. |
| `$injectionDetectionParameters` | `ApiGuardianParameters\|null` | no | Parameters to run injection detection on, grouped by location (path, query, header, cookie). Must be a subset of the endpoint's available parameters. |
| `$detectParameterXss` | `bool\|null` | no | Enable XSS detection on the selected injection detection parameters. |
| `$detectParameterSqli` | `bool\|null` | no | Enable SQL injection detection on the selected injection detection parameters. |
| `$rateLimitingEnabled` | `bool\|null` | no | Enable or disable per-endpoint rate limiting. |
| `$rateLimitingType` | `ApiGuardianRateLimitType\|null` | no |  |
| `$rateLimitingRequestCount` | `int\|null` | no | Maximum number of requests allowed within the timeframe before blocking. Must be greater than zero. |
| `$rateLimitingTimeframe` | `RateLimitTimeframe\|null` | no | 1 = PerSecond 10 = PerTenSeconds 60 = PerOneMinute 300 = PerFiveMinutes 900 = PerFifteenMinutes 3600 = PerOneHour |

## Returns

`ApiGuardianEndpoint`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->apiGuardian->updateEndpoint(shieldZoneId: 123, endpointId: 123);
```
