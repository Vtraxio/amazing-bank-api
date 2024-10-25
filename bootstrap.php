<?php

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->bind(Database::class, function () {
    $config = require base_path('config.php');
    $db_config = $config['database'];

    return new Database(array_diff_key($db_config, ['username' => 1, 'password' => 1]), $db_config['username'], $db_config['password']);
});

App::setContainer($container);