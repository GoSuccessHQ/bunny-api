<?php

declare(strict_types=1);

/**
 * Router for PHP's built-in web server, used by the transport tests.
 *
 * Routes:
 *   /echo             Reports method, headers, query and a digest of the body as JSON.
 *   /status/{code}    Responds with the given status and a JSON error body.
 *   /download/{bytes} Streams the given number of deterministic bytes.
 *   /sleep/{ms}       Waits before responding.
 *   /tus/{scenario}   A minimal TUS server (creation, checksum, termination); see tus().
 */

$path = parse_url(is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
$path = is_string($path) ? $path : '/';
$segments = explode('/', trim($path, '/'));

$headers = [];

foreach ($_SERVER as $key => $value) {
    if (is_string($key) && is_string($value) && str_starts_with($key, 'HTTP_')) {
        $headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
    }
}

if (is_string($_SERVER['CONTENT_TYPE'] ?? null)) {
    $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
}

if (is_string($_SERVER['CONTENT_LENGTH'] ?? null)) {
    $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
}

switch ($segments[0]) {
    case 'echo':
        $body = (string) file_get_contents('php://input');
        header('Content-Type: application/json');
        header('X-Custom: one');
        echo json_encode([
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'query' => $_SERVER['QUERY_STRING'] ?? '',
            'headers' => $headers,
            'bodyLength' => strlen($body),
            'bodySha256' => hash('sha256', $body),
            'body' => strlen($body) <= 1024 ? $body : null,
        ]);
        break;

    case 'status':
        $code = (int) ($segments[1] ?? 500);
        http_response_code($code);
        header('Content-Type: application/json');
        header('cdn-requestid: test-request-id');

        if ($code === 429) {
            header('Retry-After: 2');
        }

        echo json_encode(['ErrorKey' => 'test.error', 'Field' => 'Name', 'Message' => "Status {$code}"]);
        break;

    case 'download':
        $bytes = (int) ($segments[1] ?? 0);
        header('Content-Type: application/octet-stream');
        header("Content-Length: {$bytes}");
        $chunk = str_repeat('0123456789abcdef', 4096);

        for ($sent = 0; $sent < $bytes; $sent += strlen($chunk)) {
            echo substr($chunk, 0, min(strlen($chunk), $bytes - $sent));
        }
        break;

    case 'sleep':
        usleep(((int) ($segments[1] ?? 0)) * 1000);
        echo 'late';
        break;

    case 'tus':
        tus(array_slice($segments, 1), $headers);
        break;

    default:
        http_response_code(404);
        echo 'not found';
}

/**
 * A minimal TUS 1.0.0 server that insists on the Bunny signature headers.
 *
 *   POST   /tus/{scenario}   Create an upload; "interrupt" stores only half of the
 *                            second chunk and fails with 500, "break" rejects the
 *                            third chunk once with 403.
 *   HEAD   /tus/files/{id}   The offset of an upload.
 *   PATCH  /tus/files/{id}   Append a chunk; verifies offset and SHA-1 checksum.
 *   DELETE /tus/files/{id}   Terminate an upload.
 *   GET    /tus/files/{id}   The SHA-256 of the received data, as JSON.
 *
 * @param list<string>          $segments
 * @param array<string, string> $headers
 */
function tus(array $segments, array $headers): void
{
    $directory = sys_get_temp_dir() . '/bunny-api-tus-test';
    @mkdir($directory);
    header('Tus-Resumable: 1.0.0');

    if (($headers['tus-resumable'] ?? '') !== '1.0.0') {
        http_response_code(412);

        return;
    }

    foreach (['authorizationsignature', 'authorizationexpire', 'libraryid', 'videoid'] as $name) {
        if (($headers[$name] ?? '') === '') {
            http_response_code(401);
            echo "Missing {$name}";

            return;
        }
    }

    $method = $_SERVER['REQUEST_METHOD'] ?? '';

    if (($segments[0] ?? '') !== 'files') {
        if ($method !== 'POST') {
            http_response_code(405);

            return;
        }

        $id = bin2hex(random_bytes(8));
        file_put_contents("{$directory}/{$id}.bin", '');
        file_put_contents("{$directory}/{$id}.json", json_encode([
            'length' => (int) ($headers['upload-length'] ?? 0),
            'metadata' => $headers['upload-metadata'] ?? '',
            'scenario' => $segments[0] ?? '',
            'patches' => 0,
        ]));
        http_response_code(201);
        header("Location: /tus/files/{$id}");

        return;
    }

    $id = preg_replace('/[^a-f0-9]/', '', $segments[1] ?? '');
    $data = "{$directory}/{$id}.bin";
    $metaFile = "{$directory}/{$id}.json";

    if ($id === '' || !is_file($metaFile)) {
        http_response_code(404);

        return;
    }

    $meta = json_decode((string) file_get_contents($metaFile), true);
    $meta = is_array($meta) ? $meta : [];
    $length = is_int($meta['length'] ?? null) ? $meta['length'] : 0;
    $patches = is_int($meta['patches'] ?? null) ? $meta['patches'] : 0;
    clearstatcache();
    $offset = (int) filesize($data);

    switch ($method) {
        case 'HEAD':
            header("Upload-Offset: {$offset}");
            header("Upload-Length: {$length}");
            header('Cache-Control: no-store');
            break;

        case 'GET':
            header('Content-Type: application/json');
            echo json_encode(['offset' => $offset, 'sha256' => hash_file('sha256', $data), 'metadata' => $meta['metadata'] ?? '']);
            break;

        case 'DELETE':
            unlink($data);
            unlink($metaFile);
            http_response_code(204);
            break;

        case 'PATCH':
            $body = (string) file_get_contents('php://input');
            $meta['patches'] = ++$patches;
            file_put_contents($metaFile, json_encode($meta));

            if (($headers['content-type'] ?? '') !== 'application/offset+octet-stream') {
                http_response_code(415);
                break;
            }

            if ((int) ($headers['upload-offset'] ?? -1) !== $offset) {
                http_response_code(409);
                break;
            }

            $checksum = $headers['upload-checksum'] ?? '';

            if ($checksum !== '' && $checksum !== 'sha1 ' . base64_encode(sha1($body, true))) {
                http_response_code(460);
                break;
            }

            if (($meta['scenario'] ?? '') === 'interrupt' && $patches === 2) {
                // As if the connection broke midway: half of the chunk arrived.
                file_put_contents($data, substr($body, 0, intdiv(strlen($body), 2)), FILE_APPEND);
                http_response_code(500);
                break;
            }

            if (($meta['scenario'] ?? '') === 'break' && $patches === 3) {
                http_response_code(403);
                break;
            }

            file_put_contents($data, $body, FILE_APPEND);
            http_response_code(204);
            header('Upload-Offset: ' . ($offset + strlen($body)));
            break;

        default:
            http_response_code(405);
    }
}
