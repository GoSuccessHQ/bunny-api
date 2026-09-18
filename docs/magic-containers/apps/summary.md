# `$bunny->magicContainers->apps->summary()`

> Magic Containers API · `GET /apps/{appId}/summary`

Get Application Usage Summary

Retrieves usage summary for an application including latency, volume size, monthly cost, and status information.

## Signature

```php
public function summary(string $appId): UsageSummary
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`UsageSummary`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->summary(appId: '00000000-0000-0000-0000-000000000000');
```
