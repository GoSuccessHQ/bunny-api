<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Generated;

use GoSuccess\Bunny\Model\RequestModel;
use GoSuccess\Bunny\Model\ResponseModel;
use GoSuccess\Bunny\Tests\Support\Generated\GeneratedApis;
use GoSuccess\Bunny\Tests\Support\Generated\Samples;
use GoSuccess\Bunny\Tools\Generator\Definition\ModelDefinition;
use GoSuccess\Bunny\Tools\Generator\PhpType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Checks every generated model against the specification it was generated
 * from: each property is read from its JSON key with its type, and request
 * payloads contain exactly what the caller provided.
 */
#[CoversNothing]
final class GeneratedModelsTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function responseModels(): iterable
    {
        foreach (GeneratedApis::all() as $api => $analysis) {
            foreach ($analysis->registry->models as $model) {
                if ($model->response) {
                    yield "{$api} {$model->source}" => [$api, $model->class];
                }
            }
        }
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function requestModels(): iterable
    {
        foreach (GeneratedApis::all() as $api => $analysis) {
            foreach ($analysis->registry->models as $model) {
                if ($model->request) {
                    yield "{$api} {$model->source}" => [$api, $model->class];
                }
            }
        }
    }

    #[DataProvider('responseModels')]
    public function testReadsEveryPropertyFromItsJsonKey(string $api, string $class): void
    {
        [$model, $samples] = $this->definition($api, $class);
        $payload = $samples->payload($model);

        $instance = $class::fromArray($payload);
        self::assertInstanceOf(ResponseModel::class, $instance);

        foreach ($model->properties as $property) {
            $actual = $instance->{$property->phpName};

            if (!\array_key_exists($property->jsonName, $payload)) {
                continue;
            }

            $this->assertRead($samples, $property->type, $payload[$property->jsonName], $actual, "{$class}::\${$property->phpName}");
        }
    }

    #[DataProvider('responseModels')]
    public function testToleratesAnEmptyPayload(string $api, string $class): void
    {
        self::assertInstanceOf(ResponseModel::class, $class::fromArray([]));
    }

    #[DataProvider('requestModels')]
    public function testSerializesEveryProvidedProperty(string $api, string $class): void
    {
        [$model, $samples] = $this->definition($api, $class);

        $instance = $samples->requestModel($model);

        self::assertEquals(
            Samples::normalize($samples->payload($model, forRequest: true)),
            Samples::normalize($instance->toArray()),
        );
    }

    #[DataProvider('requestModels')]
    public function testOmitsPropertiesThatWereNotProvided(string $api, string $class): void
    {
        [$model] = $this->definition($api, $class);
        $required = array_filter($model->properties, static fn($property): bool => $property->required && !$property->readOnly);

        if ($required !== [] && !$model->response) {
            self::markTestSkipped('The model has required properties.');
        }

        $instance = new $class();
        self::assertInstanceOf(RequestModel::class, $instance);
        self::assertSame([], $instance->toArray());
    }

    /**
     * @return array{ModelDefinition, Samples}
     */
    private function definition(string $api, string $class): array
    {
        $registry = GeneratedApis::all()[$api]->registry;

        return [$registry->models[$class], new Samples($registry)];
    }

    private function assertRead(Samples $samples, PhpType $type, mixed $json, mixed $actual, string $context): void
    {
        match ($type->kind) {
            PhpType::DATE => self::assertEquals($samples->read($type, $json), $actual, $context),
            PhpType::ENUM => self::assertSame($samples->read($type, $json), $actual, $context),
            PhpType::MODEL => self::assertInstanceOf(Samples::classOf($type), $actual, $context),
            PhpType::LIST, PhpType::MAP => $this->assertCollection($samples, $type, $json, $actual, $context),
            default => self::assertSame($json, $actual, $context),
        };
    }

    private function assertCollection(Samples $samples, PhpType $type, mixed $json, mixed $actual, string $context): void
    {
        self::assertIsArray($json, $context);
        self::assertIsArray($actual, $context);
        self::assertSame(array_keys($json), array_keys($actual), $context);

        foreach ($json as $key => $item) {
            $this->assertRead($samples, $type->itemOrFail(), $item, $actual[$key], "{$context}[{$key}]");
        }
    }
}
