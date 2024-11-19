<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function dd($value): void {
    var_dump($value);

    die();
}

#[NoReturn]
function abort($status = 404): void {
    http_response_code($status);

    die();
}

function base_path($path) {
    return BASE_PATH . $path;
}

function interpolateQuery($query, $params) {
    foreach ($params as $key => $value) {
        $placeholder = is_string($key) ? ":$key" : '?';
        $escapedValue = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
        $query = preg_replace('/' . preg_quote($placeholder, '/') . '/', $escapedValue, $query, 1);
    }
    return $query;
}