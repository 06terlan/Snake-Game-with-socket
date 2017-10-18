<?php

/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

error_reporting(E_ALL);
ob_implicit_flush();

require_once __DIR__ . '/Socket/autoloader.php'; // Autoload files using Composer autoload

set_time_limit(0);


//game
$fps = 300;

$game = new \Game\Game( $fps );
// variables
$address = '127.0.0.1';
$port = 5555;
$verboseMode = true;

$server = new \WebSocket\Server( $address , $port , $verboseMode , $game );
$server->run();
$server->shutDownServer();

?>