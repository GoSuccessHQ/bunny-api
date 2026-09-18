# `pullZones->get()`

> Core Platform API · `GET /pullzone/{id}`

Get Pull Zone

## Signature

```php
public function get(int $id, ?bool $includeCertificate = null): PullZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Pull Zone that should be returned |
| `$includeCertificate` | `bool\|null` | no | Determines if the result hostnames should contain the SSL certificate |

## Returns

`PullZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->get(id: 123);
```
