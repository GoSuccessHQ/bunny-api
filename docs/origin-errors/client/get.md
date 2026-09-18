# `$bunny->originErrors->get()`

> Origin Errors API · `GET /{pullZoneId}/{dateTime}`

Get the origin errors of a pull zone on one day.

## Signature

```php
public function get(int $pullZoneId, DateTimeInterface $date): OriginErrorLog
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the pull zone. |
| `$date` | `DateTimeInterface` | yes | The day, taken in UTC; only recent days are retained. |

## Returns

`OriginErrorLog`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->originErrors->get(pullZoneId: 123, date: new DateTimeImmutable('-7 days'));
```
