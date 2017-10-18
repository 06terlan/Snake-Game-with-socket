<?php
namespace Game;

/**
 * 
 */
class Snake
{
	private $isPlaying;
	private $pressedKey;

	function __construct()
	{
		$this->isPlaying = false;
		$this->pressedKey = 'right';
	}

	public function getPressedKey(){ return $this->pressedKey; }
	public function setPressedKey($key){ if( $this->isPlaying ) $this->pressedKey = $key; }
}