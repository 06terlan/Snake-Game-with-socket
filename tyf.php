<?php

/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

error_reporting(E_ALL);
//ob_implicit_flush();

require_once __DIR__ . '/Socket/autoloader.php'; // Autoload files using Composer autoload

set_time_limit(0);

// variables
$address = '127.0.0.1';
$port = 5555;
$verboseMode = true;

//game
$fps = 200;
$snakeSize = 5;
$height = 600;
$width = 800;
$cw = 10;

$game = new \Game\Game( $fps , $snakeSize , $height , $width , $cw , $address , $port );


$SERVER = new \WebSocket\Server( $address , $port , $verboseMode , $game );
$SERVER->run();
$SERVER->shutDownServer();

?>