<?php

$mapping = [
    'WebSocket\Server' => __DIR__ . '/Server.php',
    'WebSocket\BaseServer' => __DIR__ . '/BaseServer.php'
];

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        include $mapping[$class];
    }
}, true);
