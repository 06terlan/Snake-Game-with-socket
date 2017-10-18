<?php
namespace Game;

/**
* Game
*/
class Game
{
	private $fps;
	public $clients;
	private $pid;
	
	function __construct( $fps = 300 )
	{
		$this->fps = $fps;
		$this->pid = 0;
	}

	public function action( $client , $action )
	{
		if( $action['action'] == 'pressKey' ) $this->pressKey( $client , $action['key'] );
		var_dump($this->clients);
	}

	public function play()
	{
		$this->pid = \pcntl_fork(); var_dump("pid",$this->pid);
		while ( true )
		{
			usleep(1000 * $this->fps);
			var_dump("expression");
		}
	}

	public function stop()
	{
		if( $this->pid > 0 ) posix_kill($this->pid, 0);
	}

	/*********** ACTIONS ***************/
	private function pressKey( $client , $key )
	{
		$client->snake->setPressedKey( $key );
	}
}