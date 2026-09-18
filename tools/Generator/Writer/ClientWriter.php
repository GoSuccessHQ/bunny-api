<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Writer;

use GoSuccess\Bunny\Tools\Generator\Config\ApiConfig;
use GoSuccess\Bunny\Tools\Generator\Definition\ResourceDefinition;

/**
 * Renders the client class of an API.
 *
 * Resources are exposed as properties that are created on first access
 * (property hooks), so constructing a client costs nothing for the resources
 * that are never used.
 */
final class ClientWriter
{
    public function __construct(private readonly ApiConfig $config) {}

    /**
     * @param list<ResourceDefinition> $resources
     */
    public function render(array $resources, string $source, string $description): string
    {
        $namespace = "GoSuccess\\Bunny\\{$this->config->namespace}";
        $file = new CodeFile($namespace, $this->config->client);

        $options = $file->alias('GoSuccess\\Bunny\\ClientOptions');
        $connection = $file->alias('GoSuccess\\Bunny\\Http\\Connection');
        $curl = $file->alias('GoSuccess\\Bunny\\Http\\CurlHttpClient');
        $httpClient = $file->alias('GoSuccess\\Bunny\\Http\\HttpClient');
        $limiter = $file->alias('GoSuccess\\Bunny\\RateLimit\\RateLimiter');
        $nullLimiter = $file->alias('GoSuccess\\Bunny\\RateLimit\\NullRateLimiter');
        $sensitive = $file->alias('SensitiveParameter');

        $properties = '';
        $resourceArguments = implode('', array_map(static fn(string $name): string => ", \$this->{$name}", array_keys($this->config->clientParameters)));

        foreach ($resources as $resource) {
            $class = $file->alias($resource->class);
            $property = $resource->config->property;
            $properties .= Doc::block([[$resource->config->description]], '    ');
            $properties .= "    public private(set) {$class} \${$property} {\n        get => \$this->{$property} ??= new {$class}(\$this->connection{$resourceArguments});\n    }\n\n";
        }

        $credential = $this->config->credential;
        $doc = Doc::block([
            Doc::lines($description),
            ['Resources are created on first access, so unused ones cost nothing.'],
        ]);
        $clientParameters = [];

        foreach ($this->config->clientParameters as $name => $parameter) {
            $clientParameters[] = [$parameter['type'], "\${$name}", $parameter['description']];
        }

        $parameters = [
            ...$clientParameters,
            ['string', "\${$credential}", $this->config->credentialDescription],
            ['ClientOptions', '$options', 'Timeouts, retries and user agent.'],
            ['HttpClient|null', '$httpClient', 'Custom transport; defaults to the built-in cURL transport.'],
            ['RateLimiter', '$rateLimiter', 'Client-side throttling; disabled by default.'],
            ['string', '$baseUri', 'Base URI of the API.'],
        ];
        $typeWidth = max(array_map(static fn(array $parameter): int => \strlen($parameter[0]), $parameters));
        $nameWidth = max(array_map(static fn(array $parameter): int => \strlen($parameter[1]), $parameters));
        $constructorDoc = Doc::block([array_map(
            static fn(array $parameter): string => '@param ' . str_pad($parameter[0], $typeWidth) . ' ' . str_pad($parameter[1], $nameWidth) . " {$parameter[2]}",
            $parameters,
        )], '    ');

        $errorStatus = '';

        if ($this->config->errorStatus !== null) {
            [$class, $function] = explode('::', $this->config->errorStatus, 2);
            $errorStatus = '            errorStatus: ' . $file->alias("GoSuccess\\Bunny\\{$this->config->namespace}\\{$class}") . "::{$function}(...),\n";
        }

        $promoted = '';

        foreach ($this->config->clientParameters as $name => $parameter) {
            $promoted .= "        public readonly {$parameter['type']} \${$name},\n";
        }

        $body = "{$doc}final class {$this->config->client}\n{\n"
            . "    public const string DEFAULT_BASE_URI = '{$this->config->baseUri}';\n\n"
            . $properties
            . "    private readonly {$connection} \$connection;\n\n"
            . $constructorDoc
            . "    public function __construct(\n"
            . $promoted
            . "        #[{$sensitive}]\n"
            . "        string \${$credential},\n"
            . "        {$options} \$options = new {$options}(),\n"
            . "        ?{$httpClient} \$httpClient = null,\n"
            . "        {$limiter} \$rateLimiter = new {$nullLimiter}(),\n"
            . "        string \$baseUri = self::DEFAULT_BASE_URI,\n"
            . "    ) {\n"
            . "        \$this->connection = new {$connection}(\n"
            . "            \$baseUri,\n"
            . "            \${$credential},\n"
            . "            \$options,\n"
            . "            \$httpClient ?? new {$curl}(\$options->timeout, \$options->connectTimeout),\n"
            . "            \$rateLimiter,\n"
            . $errorStatus
            . "        );\n"
            . "    }\n}\n";

        return $file->render($body, $source);
    }
}
