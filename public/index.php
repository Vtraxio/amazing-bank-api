<?php

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'Core/Functions.php';

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require base_path("$class.php");
});

// ^ Auto loader ^

use Core\HttpStatusCode;
use Core\Router;
use Core\HttpException;

require base_path('bootstrap.php');

$router = new Router();
$config = require base_path('config.php');
require base_path('routes.php');

header("Content-type: application/json");

try {
    $router->route($config['misc']['base_path'], strtok($_SERVER['REQUEST_URI'], '?'), $_SERVER['REQUEST_METHOD']);
} catch (HttpException $exception) {
    if ($exception->json) {
        echo json_encode($exception->json);
    }
    abort($exception->statusCode->value);
} catch (Exception $exception) {
    http_response_code(HttpStatusCode::INTERNAL_SERVER_ERROR->value);
    throw $exception;
}
