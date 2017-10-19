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
class T1 extends Thread
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
        	print "Working " . $i . "<br/>\n";
        	sleep(1);
        }
    }
}

$work = new T1();
print 'Starting<br/>\n';
$work->start();
print 'Started<br/>\n';
