<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Config;

use RuntimeException;

/**
 * Generator configuration of one API, loaded from tools/config/<api>.php.
 *
 * All naming decisions and every deviation from the specification live in
 * that file, so they are explicit and reviewable.
 */
final readonly class ApiConfig
{
    /**
     * @param string                              $name              Short name, e.g. "core".
     * @param string                              $title             Human-readable API name.
     * @param string                              $spec              Snapshot name under resources/specs/.
     * @param string                              $namespace         Sub-namespace below GoSuccess\Bunny, e.g. "Core".
     * @param string                              $client            Class name of the API client.
     * @param string                              $baseUri           Default base URI.
     * @param string                              $credential        Name of the constructor argument holding the key.
     * @param array<string, string|false>         $schemas           Schema name => class name, or false to skip it.
     * @param array<string, string>               $properties        "Schema.Property" => PHP property name.
     * @param array<string, array<int|string, string>> $enumCases   Schema name => value => case name.
     * @param list<string>                        $extraModels       Schemas generated for hand-written code.
     * @param array<string, PaginationConfig>     $pagination        Pagination styles, keyed by name.
     * @param array<string, ResourceConfig>       $resources         Keyed by the client property name.
     * @param array<string, string>               $ignored           Operation id => reason for not implementing it.
     */
    public function __construct(
        public string $name,
        public string $title,
        public string $spec,
        public string $namespace,
        public string $client,
        public string $baseUri,
        public string $credential,
        public array $schemas,
        public array $properties,
        public array $enumCases,
        public array $extraModels,
        public array $pagination,
        public array $resources,
        public array $ignored,
    ) {}

    public static function load(string $file): self
    {
        $config = require $file;

        if (!\is_array($config)) {
            throw new RuntimeException("{$file} must return an array.");
        }

        $reader = new ConfigReader($config, basename($file));

        $resources = [];

        foreach ($reader->map('resources') as $property => $resource) {
            $resources[(string) $property] = ResourceConfig::fromArray((string) $property, $reader->nested($resource, "resources.{$property}"));
        }

        $pagination = [];

        foreach ($reader->map('pagination') as $style => $definition) {
            $pagination[(string) $style] = PaginationConfig::fromArray((string) $style, $reader->nested($definition, "pagination.{$style}"));
        }

        $enumCases = [];

        foreach ($reader->map('enumCases') as $schema => $cases) {
            $enumCases[(string) $schema] = $reader->nested($cases, "enumCases.{$schema}")->stringMap();
        }

        $instance = new self(
            name: $reader->string('name'),
            title: $reader->string('title'),
            spec: $reader->string('spec'),
            namespace: $reader->string('namespace'),
            client: $reader->string('client'),
            baseUri: $reader->string('baseUri'),
            credential: $reader->string('credential'),
            schemas: $reader->schemaMap('schemas'),
            properties: $reader->stringMapAt('properties'),
            enumCases: $enumCases,
            extraModels: $reader->stringList('extraModels'),
            pagination: $pagination,
            resources: $resources,
            ignored: $reader->stringMapAt('ignored'),
        );

        $reader->assertNoUnknownKeys();

        return $instance;
    }

    public function fqcn(string $subNamespace, string $class): string
    {
        return "GoSuccess\\Bunny\\{$this->namespace}\\{$subNamespace}\\{$class}";
    }

    public function directory(): string
    {
        return \dirname(__DIR__, 3) . '/src/' . str_replace('\\', '/', $this->namespace);
    }
}
