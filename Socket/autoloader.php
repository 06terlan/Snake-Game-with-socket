<?php

$mapping = [
    //'GuzzleHttp\Stream\AppendStream' => __DIR__ . '/GuzzleHttp/Stream/AppendStream.php',
    //'GuzzleHttp\Stream\CachingStream' => __DIR__ . '/GuzzleHttp/Stream/CachingStream.php'
];

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        include $mapping[$class];
    }
}, true);
