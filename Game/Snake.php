<?php
namespace Game;

/**
 * 
 */
class Snake
{
	private $isPlaying;
	private $pressedKey;
	private $length;
	private $size;

	function __construct( $size )
	{
		$this->isPlaying = false;
		$this->pressedKey = 'right';
		$this->length = [];
		$this->size = $size;
	}

	public function getPressedKey(){ return $this->pressedKey; }
	
	public function setPressedKey($key){ if( $this->isPlaying ) $this->pressedKey = $key; }
	
	public function initSnake()
	{
		for($i = 0; $i < $this->size; $i++ )
		{
			$this->length[] = [ 'x' => $i , 'y' => 0 ];
		}
	}

	public function newGame()
	{
		$this->isPlaying = true;
		$this->pressedKey = 'right';
		$this->length = [];
		$this->initSnake();
	}
}