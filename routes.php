<?php

use Controllers\AuthenticationController;
use Controllers\UserController;
use Middleware\AuthMiddleware;

$router->get("/", function () {
    echo "Lubisz chłopców";
});

$router->post('/login', [AuthenticationController::class, 'login']);

$router->get("/account/details", [UserController::class, 'showMe'])->use(AuthMiddleware::class);
$router->get("/account/:id", [UserController::class, 'showAny'])->where('id', "/^[0-9]+$/");
