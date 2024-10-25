<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function dd($value): void {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

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
