<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use GoSuccess\Bunny\Tools\Generator\Config\ApiConfig;
use GoSuccess\Bunny\Tools\Generator\Definition\ResourceDefinition;
use GoSuccess\Bunny\Tools\Generator\Writer\ClientWriter;
use GoSuccess\Bunny\Tools\Generator\Writer\CodeFile;
use GoSuccess\Bunny\Tools\Generator\Writer\EnumWriter;
use GoSuccess\Bunny\Tools\Generator\Writer\ModelWriter;
use GoSuccess\Bunny\Tools\Generator\Writer\ResourceWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * Generates the code of one API.
 */
final class Generator
{
    private readonly string $root;

    public function __construct(private readonly string $configFile)
    {
        $this->root = \dirname(__DIR__, 2);
    }

    /**
     * Build the definitions of everything the API needs, without writing files.
     */
    public function analyze(): Analysis
    {
        $config = ApiConfig::load($this->configFile);
        $spec = Spec::load($config->spec, "{$this->root}/resources/specs/{$config->spec}.json")
            ->patched($config->additionalSchemas, $config->additionalProperties);
        $this->checkSchemaNames($spec, $config);
        $registry = new Registry($spec, $config);
        $builder = new ResourceBuilder($registry);

        $resources = [];

        foreach ($config->resources as $resourceConfig) {
            $resources[] = $builder->build($resourceConfig);
        }

        foreach ($config->extraModels as $schema) {
            $registry->markUsage(new PhpType(PhpType::MODEL, $registry->requireModel($schema)), false);
        }

        $this->checkCoverage($spec, $config, $resources);

        return new Analysis($config, $registry, $resources, $builder->notes);
    }

    public function run(): string
    {
        $analysis = $this->analyze();
        $config = $analysis->config;
        $registry = $analysis->registry;
        $resources = $analysis->resources;
        $specFile = "resources/specs/{$config->spec}.json";

        $files = [];
        $modelWriter = new ModelWriter($registry);
        $enumWriter = new EnumWriter();
        $resourceWriter = new ResourceWriter($config);

        foreach ($registry->enums as $enum) {
            $files[$this->pathOf($enum->class)] = $enumWriter->render($enum, $specFile);
        }

        $models = 0;

        foreach ($registry->models as $model) {
            if ($model->request || $model->response) {
                $files[$this->pathOf($model->class)] = $modelWriter->render($model, $specFile);
                ++$models;
            }
        }

        foreach ($resources as $resource) {
            $files[$this->pathOf($resource->class)] = $resourceWriter->render($resource, $specFile);
        }

        $clientClass = "GoSuccess\\Bunny\\{$config->namespace}\\{$config->client}";
        $files[$this->pathOf($clientClass)] = new ClientWriter($config)->render($resources, $specFile, "Client for the bunny.net {$config->title} (`{$config->baseUri}`).");

        $written = $this->write($files);
        $deleted = $this->deleteStale($config, $files);

        $methods = array_sum(array_map(static fn(ResourceDefinition $resource): int => \count($resource->methods), $resources));
        $handwritten = array_sum(array_map(static fn(ResourceDefinition $resource): int => \count($resource->handwritten), $resources));
        $report = \sprintf(
            "[%s] %d enums, %d models, %d resources (%d generated methods, %d hand-written), %d files written, %d stale deleted\n",
            $config->name,
            \count($registry->enums),
            $models,
            \count($resources),
            $methods,
            $handwritten,
            $written,
            $deleted,
        );

        foreach (array_unique([...$registry->enumBuilder->notes, ...$analysis->notes]) as $note) {
            $report .= "  note: {$note}\n";
        }

        return $report;
    }

    /**
     * Schema names in the configuration must exist, or a typo would silently
     * change nothing.
     */
    private function checkSchemaNames(Spec $spec, ApiConfig $config): void
    {
        // Inline objects are addressed as "Schema.property".
        $names = array_map(static fn(string $name): string => explode('.', $name, 2)[0], [
            ...array_keys($config->schemas),
            ...array_keys($config->properties),
            ...array_keys($config->enumCases),
            ...$config->extraModels,
            ...$config->voidResponses,
            ...$config->stringMaps,
            ...$config->nullableProperties,
        ]);
        $unknown = array_unique(array_filter($names, static fn(string $name): bool => !$spec->hasSchema($name)));

        if ($unknown !== []) {
            throw new RuntimeException('Unknown schemas in the configuration: ' . implode(', ', $unknown) . '.');
        }
    }

    /**
     * Every operation must be implemented exactly once or ignored with a reason.
     *
     * @param list<ResourceDefinition> $resources
     */
    private function checkCoverage(Spec $spec, ApiConfig $config, array $resources): void
    {
        $generated = [];

        foreach ($resources as $resource) {
            foreach ($resource->methods as $method) {
                $id = $method->operation->id;
                $where = "{$resource->config->class}::{$method->config->name}";

                if (isset($generated[$id])) {
                    throw new RuntimeException("Operation {$id} is implemented twice: {$generated[$id]} and {$where}.");
                }

                $generated[$id] = $where;
            }
        }

        // Several hand-written methods may share one operation, e.g. the ways of
        // setting a Stream thumbnail; a generated method must be alone.
        $implemented = $generated;

        foreach ($resources as $resource) {
            foreach ($resource->handwritten as $method) {
                $id = $method->operation->id;

                if (isset($generated[$id])) {
                    throw new RuntimeException("Operation {$id} is both generated ({$generated[$id]}) and hand-written.");
                }

                $implemented[$id] = "{$resource->config->class}::{$method->config->name}";
            }
        }

        $problems = [];

        foreach (array_keys($spec->operations()) as $id) {
            $ignored = isset($config->ignored[$id]);

            if (!isset($implemented[$id]) && !$ignored) {
                $problems[] = "not implemented: {$id}";
            }

            if (isset($implemented[$id]) && $ignored) {
                $problems[] = "implemented but also ignored: {$id}";
            }
        }

        foreach (array_keys($config->ignored) as $id) {
            if (!isset($spec->operations()[$id])) {
                $problems[] = "ignored operation does not exist: {$id}";
            }
        }

        if ($problems !== []) {
            throw new RuntimeException("Coverage check failed:\n  " . implode("\n  ", $problems));
        }
    }

    private function pathOf(string $class): string
    {
        $relative = substr($class, \strlen('GoSuccess\\Bunny\\'));

        return "{$this->root}/src/" . str_replace('\\', '/', $relative) . '.php';
    }

    /**
     * @param array<string, string> $files
     */
    private function write(array $files): int
    {
        $written = 0;

        foreach ($files as $path => $content) {
            $directory = \dirname($path);

            if (!is_dir($directory) && !mkdir($directory, 0o775, true) && !is_dir($directory)) {
                throw new RuntimeException("Unable to create {$directory}.");
            }

            if (!is_file($path) || file_get_contents($path) !== $content) {
                file_put_contents($path, $content);
                ++$written;
            }
        }

        return $written;
    }

    /**
     * Delete generated files of this API that are no longer produced.
     *
     * @param array<string, string> $files
     */
    private function deleteStale(ApiConfig $config, array $files): int
    {
        $directory = $config->directory();

        if (!is_dir($directory)) {
            return 0;
        }

        $deleted = 0;

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo) {
                continue;
            }

            $path = $file->getPathname();

            if ($file->getExtension() !== 'php' || isset($files[$path])) {
                continue;
            }

            $content = (string) file_get_contents($path);

            if (str_contains($content, CodeFile::MARKER)) {
                unlink($path);
                ++$deleted;
            }
        }

        return $deleted;
    }
}
