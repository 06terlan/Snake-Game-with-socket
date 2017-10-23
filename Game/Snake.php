<?php
namespace Game;

/**
 * 
 */
class Snake
{
	public  $isPlaying;
	private $pressedKey;
	private $length;
	private $size;
	private $id;
	private $score;

	function __construct( $size , $id )
	{
		$this->isPlaying = false;
		$this->pressedKey = 'right';
		$this->length = [];
		$this->size = $size;
		$this->id = $id;
	}

	public function getPressedKey(){ return $this->pressedKey; }
	
	public function setPressedKey($key)
	{
		if( !$this->isPlaying
			|| $key == $this->pressedKey 
			|| ($this->pressedKey == 'left'  && $key == 'right')
			|| ($this->pressedKey == 'right' && $key == 'left')
			|| ($this->pressedKey == 'up'    && $key == 'down')
			|| ($this->pressedKey == 'down'  && $key == 'up')
			) return;
		else $this->pressedKey = $key;
	}

	public function getLength(){ return $this->length; }

	public function setLength( $length ){ $this->length = $length; }

	public function getScore(){ return $this->score; }

	public function increaseScore(){ $this->score++ ; }

	public function getId() { return $this->id; }

	public function initSnake()
	{
		for($i = $this->size - 1; $i >= 0 ; $i-- )
		{
			$this->length[] = [ 'x' => $i , 'y' => 0 ];
		}
	}

	public function newGame()
	{
		$this->isPlaying 	= true;
		$this->score     	= 0;
		$this->pressedKey 	= 'right';
		$this->length 		= [];
		$this->initSnake();
	}
}