<?php

use Controllers\AuthenticationController;
use Controllers\UserController;
use Core\Request;

$router->get("/", function () {
    echo "Lubisz chłopców";
});

$router->post('/login', [AuthenticationController::class, 'login']);

$router->get("/account/details", [UserController::class, 'showMe']);
$router->get("/account/:id", [UserController::class, 'showAny'])->where('id', "/^[0-9]+$/");
