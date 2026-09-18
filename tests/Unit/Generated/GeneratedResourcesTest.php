<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Generated;

use GoSuccess\Bunny\Http\Query;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Model\RequestModel;
use GoSuccess\Bunny\Pagination\Page;
use GoSuccess\Bunny\Pagination\Paginator;
use GoSuccess\Bunny\Tests\Support\Generated\GeneratedApis;
use GoSuccess\Bunny\Tests\Support\Generated\Samples;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use GoSuccess\Bunny\Tools\Generator\Analysis;
use GoSuccess\Bunny\Tools\Generator\Definition\MethodDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ParameterDefinition;
use GoSuccess\Bunny\Tools\Generator\PhpType;
use LogicException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Calls every generated resource method against a mocked transport and checks
 * that the request matches the specification: HTTP method, path, query
 * parameters and body; and that the response is mapped onto the declared type.
 */
#[CoversNothing]
final class GeneratedResourcesTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function methods(): iterable
    {
        foreach (GeneratedApis::all() as $api => $analysis) {
            foreach ($analysis->resources as $resource) {
                foreach ($resource->methods as $method) {
                    yield "{$api} {$resource->config->property}->{$method->config->name}()" => [$api, $resource->config->property, $method->config->name];
                }
            }
        }
    }

    #[DataProvider('methods')]
    public function testSendsTheSpecifiedRequestAndMapsTheResponse(string $api, string $property, string $name): void
    {
        $analysis = GeneratedApis::all()[$api];
        $method = $this->method($analysis, $property, $name);
        $samples = new Samples($analysis->registry);

        [$arguments, $expectedPath, $expectedQuery, $expectedBody] = $this->arguments($method, $samples);
        $http = new MockHttpClient(new Response($method->returns === null ? 204 : 200, $this->responseBody($method, $samples)));
        $resource = $this->client($analysis, $http)->{$property};

        $result = $resource->{$name}(...$arguments);

        self::assertSame(1, $http->callCount());
        $request = $http->requests[0];
        self::assertSame($method->operation->method, $request->method->value);

        $uri = parse_url($request->uri);
        self::assertIsArray($uri);
        self::assertSame('/' . $expectedPath, $uri['path'] ?? null);
        self::assertSame(Query::build($expectedQuery), $uri['query'] ?? '');

        if ($expectedBody === null) {
            self::assertNull($request->body);
        } else {
            self::assertEquals(Samples::normalize($expectedBody), $http->jsonBody());
        }

        $this->assertResult($method, $result);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function paginatedMethods(): iterable
    {
        foreach (GeneratedApis::all() as $api => $analysis) {
            foreach ($analysis->resources as $resource) {
                foreach ($resource->methods as $method) {
                    if ($method->pagination !== null && $method->config->all !== null) {
                        yield "{$api} {$resource->config->property}->{$method->config->all}()" => [$api, $resource->config->property, $method->config->name];
                    }
                }
            }
        }
    }

    #[DataProvider('paginatedMethods')]
    public function testPaginatorsIterateOverAllItems(string $api, string $property, string $name): void
    {
        $analysis = GeneratedApis::all()[$api];
        $method = $this->method($analysis, $property, $name);
        $pagination = $method->pagination ?? throw new LogicException('Not paginated.');
        $all = $method->config->all ?? throw new LogicException('No paginator method.');

        $samples = new Samples($analysis->registry);
        [$arguments] = $this->arguments($method, $samples);
        foreach ($method->parameters as $parameter) {
            if ($parameter->specName === $pagination->position || $parameter->specName === $pagination->size) {
                unset($arguments[$parameter->phpName]);
            }
        }

        $http = new MockHttpClient(new Response(200, $this->responseBody($method, $samples)));
        $paginator = $this->client($analysis, $http)->{$property}->{$all}(...$arguments);

        self::assertInstanceOf(Paginator::class, $paginator);
        self::assertCount(1, iterator_to_array($paginator));
        self::assertSame(1, $http->callCount());
    }

    private function method(Analysis $analysis, string $property, string $name): MethodDefinition
    {
        foreach ($analysis->resources as $resource) {
            if ($resource->config->property !== $property) {
                continue;
            }

            foreach ($resource->methods as $method) {
                if ($method->config->name === $name) {
                    return $method;
                }
            }
        }

        throw new LogicException("Unknown method {$property}->{$name}().");
    }

    private function client(Analysis $analysis, MockHttpClient $http): object
    {
        $class = "GoSuccess\\Bunny\\{$analysis->config->namespace}\\{$analysis->config->client}";
        $arguments = array_map(static fn(array $parameter): int|string => self::clientValue($parameter['type']), $analysis->config->clientParameters);

        return new $class(...[...$arguments, $analysis->config->credential => 'secret', 'httpClient' => $http]);
    }

    private static function clientValue(string $type): int|string
    {
        return $type === 'int' ? 7 : 'sample';
    }

    /**
     * @return array{array<string, mixed>, string, array<string, mixed>, array<array-key, mixed>|null}
     */
    private function arguments(MethodDefinition $method, Samples $samples): array
    {
        $arguments = [];
        $query = [];
        $fields = [];
        $payload = null;
        $path = ltrim($method->operation->path, '/');

        foreach ($method->parameters as $parameter) {
            $value = $samples->php($parameter->type);
            $arguments[$parameter->phpName] = $value;

            if ($parameter->location === ParameterDefinition::CLIENT) {
                unset($arguments[$parameter->phpName]);
                $path = str_replace("{{$parameter->specName}}", (string) self::clientValue($parameter->type->kind), $path);

                continue;
            }

            match ($parameter->location) {
                ParameterDefinition::PATH => $path = str_replace("{{$parameter->specName}}", rawurlencode(Query::format('path', $value)), $path),
                ParameterDefinition::QUERY => $query[$parameter->specName] = $value,
                ParameterDefinition::BODY => $fields[$parameter->specName] = $samples->json($parameter->type),
                ParameterDefinition::PAYLOAD => $payload = $value instanceof RequestModel ? $value->toArray() : (\is_array($value) ? $value : null),
                default => throw new LogicException("Unknown location {$parameter->location}."),
            };
        }

        return [$arguments, $path, $query, $payload ?? ($fields === [] ? null : $fields)];
    }

    private function responseBody(MethodDefinition $method, Samples $samples): string
    {
        if ($method->returns === null) {
            return '';
        }

        $value = $samples->json($method->returns);

        if ($method->pagination !== null) {
            $value = [$method->pagination->items => [$value]];
        } elseif ($method->unwrap !== null) {
            $value = [$method->unwrap => $value];
        }

        return json_encode($value, \JSON_THROW_ON_ERROR);
    }

    private function assertResult(MethodDefinition $method, mixed $result): void
    {
        $type = $method->returns;

        if ($type === null) {
            self::assertNull($result);

            return;
        }

        if ($method->pagination !== null) {
            self::assertInstanceOf(Page::class, $result);
            self::assertCount(1, $result->items);

            return;
        }

        match ($type->kind) {
            PhpType::MODEL, PhpType::ENUM => self::assertInstanceOf(Samples::classOf($type), $result),
            PhpType::LIST => self::assertIsList($result),
            PhpType::MAP, PhpType::OBJECT => self::assertIsArray($result),
            PhpType::STRING => self::assertIsString($result),
            PhpType::INT => self::assertIsInt($result),
            PhpType::FLOAT => self::assertIsFloat($result),
            PhpType::BOOL => self::assertIsBool($result),
            default => self::assertNotNull($result),
        };
    }
}
