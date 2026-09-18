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

    default:
        http_response_code(404);
        echo 'not found';
}
