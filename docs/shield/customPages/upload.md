# `$bunny->shield->customPages->upload()`

> Shield API · `PUT /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`

Upload the HTML of a custom page, replacing the current one.

## Signature

```php
public function upload(int $shieldZoneId, CustomPageType $type, Stream|string $html): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield zone. |
| `$type` | `CustomPageType` | yes | The page. |
| `$html` | `string\|Stream` | yes | The HTML of the page. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Shield\Enum\CustomPageType;

$bunny = new Bunny('your-api-key');

$bunny->shield->customPages->upload(shieldZoneId: 123, type: CustomPageType::Block, html: Stream::fromFile('path/to/file'));
```
