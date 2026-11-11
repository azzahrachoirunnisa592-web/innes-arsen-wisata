<?php

$publicPath = realpath(__DIR__.'/public');
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if ($uri !== '/') {
    $requestedPath = realpath($publicPath.DIRECTORY_SEPARATOR.ltrim($uri, '/\\'));

    if ($requestedPath !== false
        && str_starts_with($requestedPath, $publicPath.DIRECTORY_SEPARATOR)
        && is_file($requestedPath)) {
        return false;
    }
}

require $publicPath.'/index.php';
