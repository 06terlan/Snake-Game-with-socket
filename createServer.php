<?php
/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

require __DIR__ . '/Socket/autoloader.php'; // auto loader

error_reporting(E_ALL);
set_time_limit(0);


// variables
$address = '127.0.0.1';
$port = 5001;
$cprint = true;

$server = new \WebSocket\Server($address, $port, $cprint);
//$server->run();