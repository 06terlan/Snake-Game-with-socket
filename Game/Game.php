<?php
namespace Game;

/**
* Game
*/

class Game extends \Thread
{
	private $fps;
	private $shutDown;
	private $snakeSize;
	public  $server;
	private $width;
	private $height;
	private $cw;

	private $address;
	private $port;

	private $foodX;
	private $foodY;
	private $foodEaten;
	
	function __construct( $fps = 300 , $snakeSize = 5 , $height = 400 , $width = 520 , $cw = 10 , $address = '127.0.0.1' , $port = 5555 )
	{
		$this->fps = $fps;
		$this->shutDown = false;
		$this->snakeSize = $snakeSize;
		$this->height = $height;
		$this->width = $width;
		$this->cw = $cw;

		$this->address = $address;
		$this->port = $port;

		$this->Newfood();
		$this->foodEaten = false;
	}

	public function run()
	{
		$this->shutDown = false;
		$socket = $this->createSocketConnection();

        while ( !$this->shutDown )
		{
			
			$data = json_encode([ 'action' => 'nextMove' , 'MyOwnServerRequest' => 1 ] , true);
			socket_write($socket, $data, strlen($data));

			usleep( 1000 * $this->fps );
		}
	}

	public function stop()
	{
		$this->shutDown = true;
	}

	/*********** Getter & setter ***************/

	public function getSnakeSize(){ return $this->snakeSize; }

	/*********** ACTIONS ***************/
	protected function createSocketConnection( )
	{
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false)
		{
		    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		}
		else
		{
		    echo "Game socket_create OK.\n";
		}
		$result = socket_connect($socket, '127.0.0.1' , 5555 );
		if ($result === false)
		{
		    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
		}
		else
		{
		    echo "Game socket_connect OK.\n";
		}

		return $socket;
	}

	public function nextMove( &$snake )
	{
		$length = $snake->getLength();

		$nx = $length[0]['x'];
		$ny = $length[0]['y'];

		switch ($snake->getPressedKey())
		{
	      case 'right':
	        $nx++;
	        break;
	      case 'left':
	        $nx--;
	        break;
	      case 'up':
	        $ny--;
	        break;
	      case 'down':
	        $ny++;
	        break;
	    }

	    if( $this->collision( $nx , $ny ) )
	    {
	    	$snake->isPlaying = false;
	    	return [ 'isPlaying' => false ];
	    }
	    else if( $this->foodX == $nx && $this->foodY == $ny )
	    {
	    	$snake->increaseScore();
	    	$this->foodEaten = true;
	    }
	    else array_pop($length);
	    
	    array_unshift($length, [ 'x' => $nx , 'y' => $ny ]);

	    $snake->setLength($length);

	    return [ 'length' => $length , 'score' => $snake->getScore() , 'isPlaying' => true ];
	}

	public function collision($nx, $ny)
	{
	    if ($nx == -1 || $nx == ($this->width / $this->cw) || $ny == -1 || $ny == ($this->height / $this->cw))
	    {
	      return true;
	    }
	    return false;    
	}

	public function Newfood()
	{
		$this->foodEaten = false;
		
		$this->foodX = rand( 0 , ($this->width / $this->cw) );
		$this->foodY = rand( 0 , ($this->height / $this->cw) );
	}

	public function getFood()
	{
		if( $this->foodEaten )
		{
			$this->Newfood();
		}
		return [ 'x' => $this->foodX , 'y' => $this->foodY ];
	}
}