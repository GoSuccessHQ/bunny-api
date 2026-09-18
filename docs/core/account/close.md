# `account->close()`

> Core Platform API · `POST /user/closeaccount`

Close the account

Close the current user account

Irreversible: this closes the whole bunny.net account.

## Signature

```php
public function close(?string $password = null, ?string $reason = null): CloseAccountResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$password` | `string\|null` | no |  |
| `$reason` | `string\|null` | no |  |

## Returns

`CloseAccountResult`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->account->close();
```
