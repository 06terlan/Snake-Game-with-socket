<?php


//$aa =  (int)(microtime(true) * 1000 );
//
//usleep(1000 * 59);
//
//print (int)(microtime(true) * 1000 ) - $aa;

//ob_implicit_flush();

/**
* Thread
*/
/*class T1 extends Thread
{
	
	function __construct()
	{
		# code...
	}

	public function run()
	{
		$i = 0;
        while ($i <= 10)
        {
        	$i++;

			$aa =  (int)(microtime(true) * 1000 );
			usleep(1000 * 59);
			print (int)(microtime(true) * 1000 ) - $aa;

        	print "Working " . $i . (int)(microtime(true) * 1000 ) - $aa . "<br/>\n";
        }
    }
}

$work = new T1();
print 'Starting<br/>\n';
$work->start();
print 'Started<br/>\n';*/


class GG
{

}

class GG1
{
	public static $arr;
}

class Game extends GG
{
	public $server;

	public function dump()
	{
		var_dump($this->server->arr);
		print "<br>";
	}
}


class Server extends GG1
{
	//public $arr;

	function __construct(Game $g)
	{
		$this->arr[] = 1;
		$g->server = &$this;
		$this->arr[] = 2;
	}
}

$gg = new Game();
$ss = new Server( $gg );

$gg->dump();
$ss->arr[] = 3;
$gg->dump();