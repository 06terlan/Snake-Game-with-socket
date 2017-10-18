<?php
/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

namespace WebSocket;

class Server extends BaseServer
{
  private $game;

  function __construct( $address , $port , $cprint , \Game\Game $game )
  {
    parent::__construct( $address , $port , $cprint );

    $this->game = $game;
    $this->game->clients = &$this->clients;
    $this->game->play();
  }

  protected function process ($client, $message)
  {
    $this->consoleWrite($message);
    $action = json_decode( $message , true );

    $this->game->action($client, $action);
  }

  public function shutDownServer()
  {
    parent::shutDownServer();
    
    $this->game->stop();
  }

  protected function connecting($client)
  {
    //  after the instance of the User is created
  }
  
  protected function connected ($client)
  {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
    $this->send($client,"Hi new client");
  }
  
  protected function closed ($client)
  {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}