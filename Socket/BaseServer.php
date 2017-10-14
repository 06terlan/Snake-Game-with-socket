<?php
/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

namespace WebSocket;

abstract class BaseServer
{
	/**
	 * The address of the server
	 * @var String
	 */
	private $address;

	/**
	 * The port for the master socket
	 * @var int
	 */
	private $port;

	/**
	 * The master socket
	 * @var Resource
	 */
	private $master;

	/**
	 * The array of sockets (1 socket = 1 client)
	 * @var Array of resource
	 */
	private $sockets;

	/**
	 * The array of connected clients
	 * @var Array of clients
	 */
	private $clients;

	/**
	 * If true, the server will print messages to the terminal
	 * @var Boolean
	 */
	private $cprint;

	/**
	 * Color for terminal
	 * @var array
	 */
	private $color;

	/**
	 * Server constructor
	 * @param $address The address IP or hostname of the server (default: 127.0.0.1).
	 * @param $port The port for the master socket (default: 5001)
	 * @param $cprint The mode of BaseServer class (default: false)
	 */
	public function __construct($address = '127.0.0.1', $port = 5001, $cprint = false) 
	{
		$this->address = $address;
		$this->port = $port;
		$this->cprint = $cprint;
		$this->console("Server starting...");

		$this->color = 
		[
			'red' => '41'
		];

		/**
		 * socket creation
		 * @values AF_INET - IPv4 Internet based protocols
		 * @values SOCK_STREAM - sequenced, reliable, full-duplex, connection-based byte streams.
		 * @values SOL_TCP - reliable, connection based, stream oriented, full duplex protocol
		 */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) 
								and $this->console("socket_create() done: ")
								or $this->console("socket_create() failed: ".socket_strerror(socket_last_error()),true);
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)
								and $this->console("socket_set_option() done: ")
								or $this->console("socket_set_option() failed: ".socket_strerror(socket_last_error()),true);
		socket_bind($socket, $this->address, $this->port)
								and $this->console("socket_bind() done: ")
								or $this->console("socket_bind() failed: ".socket_strerror(socket_last_error()),true);
		socket_listen($socket, 20)
								and $this->console("socket_listen() done: ")
								or $this->console("socket_listen() failed: ".socket_strerror(socket_last_error()),true);

		$this->master = $socket;
		$this->sockets = array($socket);
		$this->console("Server started on {$this->address}:{$this->port}");

		$this->shut_down_server();
	}

	private function shut_down_server()
	{
		socket_close($this->master);
		$this->console("Server shutted down");
	}

	/**
	 * Print a text to the terminal
	 * @param $text the text to display
	 * @param $exit if true, the process will exit
	 */
	private function console($text , $exit = false , $color = null) {
		$text = date('(Y-m-d H:i:s) - ').$text."\r\n";
		if($exit) {
			die($text);
		}

		if($this->cprint) {
			if( !is_null($color) )
			{

			}
			else echo $text;
		}

		return true;
	}
}