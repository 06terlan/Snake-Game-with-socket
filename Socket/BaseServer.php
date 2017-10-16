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
		$this->consoleWrite("Server starting...");

		/**
		 * socket creation
		 * @values AF_INET - IPv4 Internet based protocols
		 * @values SOCK_STREAM - sequenced, reliable, full-duplex, connection-based byte streams.
		 * @values SOL_TCP - reliable, connection based, stream oriented, full duplex protocol
		 */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) 
								and $this->consoleWrite("socket_create() done: ")
								or $this->consoleWrite("socket_create() failed: ".socket_strerror(socket_last_error()),true);
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)
								and $this->consoleWrite("socket_set_option() done: ")
								or $this->consoleWrite("socket_set_option() failed: ".socket_strerror(socket_last_error()),true);
		socket_bind($socket, $this->address, $this->port)
								and $this->consoleWrite("socket_bind() done: ")
								or $this->consoleWrite("socket_bind() failed: ".socket_strerror(socket_last_error()),true);
		socket_listen($socket, 20)
								and $this->consoleWrite("socket_listen() done: ")
								or $this->consoleWrite("socket_listen() failed: ".socket_strerror(socket_last_error()),true);

		$this->master = $socket;
		$this->sockets = array($socket);
		$this->consoleWrite("Server started on {$this->address}:{$this->port}");
	}

	/******************** Some usefull events *************************/
	abstract protected function process($client,$message); // Called immediately when the data is recieved. 
  	abstract protected function connected($client);        // Called after the handshake response is sent to the client.
  	abstract protected function closed($client);           // Called after the connection is closed.
  	abstract protected function connecting($client);       // Called after the client instance had been created
	/******************** Some usefull events *************************/

	/**
	 * Run the server
	 */
	public function run() 
	{
		$i = 0;
		$this->consoleWrite("Start running...");
		while(true)
		{
			$changed_sockets = $this->sockets;

			@socket_select($changed_sockets, $write = NULL, $except = NULL, 1);
			foreach($changed_sockets as $s_id => $socket)
			{
				if($socket == $this->master)
				{
					($acceptedSocket = socket_accept($this->master))
						and $this->connect($acceptedSocket)
						or $this->consoleWrite("Socket error: ".socket_strerror(socket_last_error($acceptedSocket)));
				}
				else
				{
					$this->consoleWrite("Finding the socket that associated to the client...");
					$client = $this->clients[$s_id];// $this->getClientBySocket($socket);
					if($client)
					{
						$this->consoleWrite("Receiving data from the client");

						$data = null;

						$bytes = socket_recv($socket, $r_data, 2048, 0);	$data .= $r_data;
						if(!$client->getHandshake())
						{
							$this->consoleWrite("Doing the handshake");
							if(!$this->handshake($client, $data))
							{
								$this->disconnect($client);
							}
						}
						elseif($bytes === 0)
						{
							$this->disconnect($client);
						}
						else
						{
							// When received data from client
							$this->action($client, $data);
						}
					}
				}
			}

			if( file_get_contents( __DIR__ . "/action") == "exit" )
			{
				$this->shutDownServer();
				$this->consoleWrite("Action shutDownServer",true);
			}

		}
	}

	/**
	 * Do an action
	 * @param $client
	 * @param $action
	 */
	private function action($client, $data)
	{
		$data = $this->unmask($data);
		$this->consoleWrite("Performing data: ".$data);
		$this->process($client,$data);
	}

	/**
	 * Unmask a received payload
	 * @param $buffer
	 */
	private function unmask($payload) 
	{
		$length = ord($payload[1]) & 127;

		if($length == 126) {
			$masks = substr($payload, 4, 4);
			$data = substr($payload, 8);
		}
		elseif($length == 127) {
			$masks = substr($payload, 10, 4);
			$data = substr($payload, 14);
		}
		else {
			$masks = substr($payload, 2, 4);
			$data = substr($payload, 6);
		}

		$text = '';
		for($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}

	/**
	 * Disconnect a client and close the connection
	 * @param $socket
	 */
	private function disconnect($client) 
	{
		$this->consoleWrite("Disconnecting client #{$client->getId()}");
	
		unset($this->clients[$client->getId()]);
		unset($this->sockets[$client->getId()]);

		socket_shutdown($client->getSocket(), 2);
		socket_close($client->getSocket());
		$this->consoleWrite("Socket closed");

		$this->consoleWrite("Client #{$client->getId()} disconnected");
		$this->closed($client);
	}

	/**
	 * Do the handshaking between client and server
	 * @param $client
	 * @param $headers
	 */
	private function handshake($client, $headers)
	{
		$this->consoleWrite("Getting client WebSocket version...");
		if(preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
		{
			$version = $match[1];
		}
		else
		{
			$this->consoleWrite("The client doesn't support WebSocket");
			return false;
		}

		$this->consoleWrite("Client WebSocket version is {$version}, (required: 13)");
		if($version == 13)
		{
			// Extract header variables
			$this->consoleWrite("Getting headers...");
			if(preg_match("/GET (.*) HTTP/", $headers, $match))
				$root = $match[1];
			if(preg_match("/Host: (.*)\r\n/", $headers, $match))
				$host = $match[1];
			if(preg_match("/Origin: (.*)\r\n/", $headers, $match))
				$origin = $match[1];
			if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
				$key = $match[1];

			$this->consoleWrite("Client headers are:");
			$this->consoleWrite("\t- Root: ".$root);
			$this->consoleWrite("\t- Host: ".$host);
			$this->consoleWrite("\t- Origin: ".$origin);
			$this->consoleWrite("\t- Sec-WebSocket-Key: ".$key);

			$this->consoleWrite("Generating Sec-WebSocket-Accept key...");
			$acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
			$acceptKey = base64_encode(sha1($acceptKey, true));

			$upgrade = "HTTP/1.1 101 Switching Protocols\r\n".
					   "Upgrade: websocket\r\n".
					   "Connection: Upgrade\r\n".
					   "Sec-WebSocket-Accept: $acceptKey".
					   "\r\n\r\n";

			$this->consoleWrite("Sending this response to the client #{$client->getId()}:\r\n".$upgrade);
			socket_write($client->getSocket(), $upgrade);
			$client->setHandshake(true);
			$this->consoleWrite("Handshake is successfully done!");
			$this->connected($client);
			return true;
		}
		else
		{
			$this->consoleWrite("WebSocket version 13 required (the client supports version {$version})");
			return false;
		}
	}

	/**
	 * Get the client associated with the socket
	 * @param $socket
	 * @return A client object if found, if not false
	 */
	private function getClientBySocket($socket)
	{
		foreach($this->clients as $client)
			if($client->getSocket() == $socket)
			{
				$this->consoleWrite("Client found");
				return $client;
			}
		return false;
	}

	/**
	 * Create a client object with its associated socket
	 * @param $socket
	 */
	private function connect($socket)
	{
		$this->consoleWrite("Creating client...");
		$client = new \WebSocket\Client(uniqid(), $socket);
		$this->clients[$client->getId()] = $client;
		$this->sockets[$client->getId()] = $socket;
		$this->consoleWrite("Client #{$client->getId()} is successfully created!");
		$this->connecting($client);

		return true;
	}

	/**
	 * Shut down the ws server
	 */
	public function shutDownServer()
	{
		socket_close($this->master);
		$this->consoleWrite("Server shutted down",false,"red");
	}

	/**
	 * Print a text to the terminal
	 * @param $text the text to display
	 * @param $exit if true, the process will exit
	 */
	private function consoleWrite($text , $exit = false) 
	{
		$text = date('(Y-m-d H:i:s) - ').$text;
		if($exit) {
			die($text);
		}

		if($this->cprint) {
			echo $text."\r\n<br/>";
		}

		return true;
	}

	/**
	 * Send a text to client
	 * @param $client
	 * @param $text
	 */
	protected function send($client, $text)
	{
		$this->consoleWrite("Send '".$text."' to client #{$client->getId()}");
		$text = $this->encode($text);
		if(socket_write($client->getSocket(), $text, strlen($text)) === false)
		{
			$this->consoleWrite("Unable to write to client #{$client->getId()}'s socket");
			$this->disconnect($client);
		}
	}

	/**
	 * Encode a text for sending to clients via ws://
	 * @param $text
	 * @param $messageType
	 */
	function encode($message, $messageType='text')
	{
		switch ($messageType)
		{
			case 'continuous':
				$b1 = 0;
				break;
			case 'text':
				$b1 = 1;
				break;
			case 'binary':
				$b1 = 2;
				break;
			case 'close':
				$b1 = 8;
				break;
			case 'ping':
				$b1 = 9;
				break;
			case 'pong':
				$b1 = 10;
				break;
		}

		$b1 += 128;


		$length = strlen($message);
		$lengthField = "";

		if($length < 126)
		{
			$b2 = $length;
		} 
		elseif($length <= 65536)
		{
			$b2 = 126;
			$hexLength = dechex($length);
			//$this->stdout("Hex Length: $hexLength");
			if(strlen($hexLength)%2 == 1)
			{
				$hexLength = '0' . $hexLength;
			}

			$n = strlen($hexLength) - 2;

			for($i = $n; $i >= 0; $i=$i-2)
			{
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}

			while(strlen($lengthField) < 2)
			{
				$lengthField = chr(0) . $lengthField;
			}

		} else
		{

			$b2 = 127;
			$hexLength = dechex($length);

			if(strlen($hexLength) % 2 == 1)
			{
				$hexLength = '0' . $hexLength;
			}

			$n = strlen($hexLength) - 2;

			for($i = $n; $i >= 0; $i = $i - 2)
			{
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}

			while(strlen($lengthField) < 8)
			{
				$lengthField = chr(0) . $lengthField;
			}
		}

		return chr($b1) . chr($b2) . $lengthField . $message;
	}
}