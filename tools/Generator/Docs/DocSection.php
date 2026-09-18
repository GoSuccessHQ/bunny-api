<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Docs;

/**
 * The documentation of one API.
 */
final readonly class DocSection
{
    /**
     * @param string          $name     Short name, used as directory, e.g. "core".
     * @param string          $title    Human-readable name, e.g. "Core Platform API".
     * @param string          $client   Fully qualified class name of the client.
     * @param string          $accessor PHP expression reaching the client, e.g. `$bunny->core`.
     * @param list<string>    $uses     Classes the setup code needs.
     * @param string          $setup    Code that prepares the accessor, e.g. `$bunny = new Bunny('your-api-key');`.
     * @param list<DocTarget> $targets
     */
    public function __construct(
        public string $name,
        public string $title,
        public string $client,
        public string $accessor,
        public array $uses,
        public string $setup,
        public array $targets,
    ) {}
}
