# `$bunny->core->purge->url()`

> Core Platform API · `POST /purge`

Purge URL

## Signature

```php
public function url(string $url, ?bool $async = null, ?bool $exactPath = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$url` | `string` | yes | The URL that will be purged from cache. |
| `$async` | `bool\|null` | no | (Optional) Determines if the call should wait for the purge logic to complete |
| `$exactPath` | `bool\|null` | no | (Optional) When true and the URL ends with '/', purges only the exact path without adding a wildcard suffix. Only applies when the pull zone has IgnoreQueryStrings disabled. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->purge->url(url: 'https://example.com/');
```
