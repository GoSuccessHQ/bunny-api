# `$bunny->magicContainers->apps->overview()`

> Magic Containers API · `GET /apps/{appId}/overview`

Get Application Overview

Retrieves comprehensive status overview for an application including latency, CPU/RAM usage, active instances, regions, and cost information.

## Signature

```php
public function overview(string $appId): Overview
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`Overview`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->overview(appId: '00000000-0000-0000-0000-000000000000');
```
