# `$bunny->core->videoLibraries->update()`

> Core Platform API · `POST /videolibrary/{id}`

Update Video Library

## Signature

```php
public function update(int $id, VideoLibraryUpdate $changes): VideoLibrary
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Video Library to update |
| `$changes` | `VideoLibraryUpdate` | yes |  |

## Returns

`VideoLibrary`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\VideoLibraryUpdate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->update(id: 123, changes: new VideoLibraryUpdate(/* ... */));
```
