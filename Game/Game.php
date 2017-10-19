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
	public $server;
	
	function __construct( $fps = 300 , $snakeSize = 5 )
	{
		$this->fps = $fps;
		$this->shutDown = false;
		$this->snakeSize = $snakeSize;
	}

	public function action( $client , $action , $clients )
	{
		if( $action['action'] == 'pressKey' ) $client->snake->setPressedKey( $action['key'] );
		else if( $action['action'] == 'newGame' ) $client->snake->newGame();
	}

	public function run()
	{
		//global $SERVER; var_dump($SERVER);
		$this->shutDown = false;

		while ( !$this->shutDown )
		{
			var_dump($this->server->clients); print "<br/>";
			/*foreach ($this->server->clients as $client)
			{
				if( $client->snake->isPlaying )
				{
					var_dump( $client->snake->length );
				}
			}*/

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
	/*protected function pressKey( $client , $key )
	{
		$client->snake->setPressedKey( $key );
	}*/
}