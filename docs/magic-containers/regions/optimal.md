# `$bunny->magicContainers->regions->optimal()`

> Magic Containers API · `GET /regions/optimal`

Get Optimal Base Region

Returns the optimal base region for deployment based on the user's CDN server token location.

## Signature

```php
public function optimal(?string $cdnServerToken = null): Region
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$cdnServerToken` | `string\|null` | no |  |

## Returns

`Region`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->regions->optimal();
```
