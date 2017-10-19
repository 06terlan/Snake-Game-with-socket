<?php
namespace Game;

/**
* Game
*/
class Game extends \Thread
{
	private $fps;
	//public $clients;
	private $lastTime;
	private $shutDown;
	
	function __construct( $fps = 300 )
	{
		$this->fps = $fps;
		$this->shutDown = false;
		$this->lastTime = $this->get_ms();
	}

	public function get_ms()
	{
		return (int)(microtime(true) * 1000 );
	}

	public function action( $client , $action , $clients )
	{
		if( $action['action'] == 'pressKey' ) $this->pressKey( $client , $action['key'] );
	}

	public function run()
	{
		$this->shutDown = false;

		while (!$this->shutDown)
		{
			var_dump("move",$this->get_ms() , $this->lastTime , $this->get_ms() - $this->lastTime);
			$this->lastTime = $this->get_ms();

			usleep( 1000 * $this->fps );
		}
	}

	public function stop()
	{
		$this->shutDown = true;
	}

	/*********** ACTIONS ***************/
	private function pressKey( $client , $key )
	{
		$client->snake->setPressedKey( $key );
	}
}