<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Resource;

use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Model\ResponseModel;

/**
 * Base class of all API resources.
 */
abstract class AbstractResource
{
    public function __construct(protected readonly Connection $connection) {}

    /**
     * Map a decoded response onto a model, rejecting anything but a JSON object.
     *
     * @template T of ResponseModel
     *
     * @param class-string<T> $model
     *
     * @return T
     */
    protected static function toModel(string $model, mixed $data): ResponseModel
    {
        if (!\is_array($data)) {
            $type = get_debug_type($data);

            throw new SerializationException("Expected a JSON object for {$model}, got {$type}.");
        }

        return $model::fromArray($data);
    }

    /**
     * Map a decoded JSON array of objects onto a list of models.
     *
     * @template T of ResponseModel
     *
     * @param class-string<T> $model
     *
     * @return list<T>
     */
    protected static function toModelList(string $model, mixed $data): array
    {
        if (!\is_array($data) || !array_is_list($data)) {
            $type = get_debug_type($data);

            throw new SerializationException("Expected a JSON array of {$model}, got {$type}.");
        }

        $list = [];

        foreach ($data as $item) {
            $list[] = self::toModel($model, $item);
        }

        return $list;
    }
}
