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
  }

  protected function process ($client, $message)
  {
    $this->consoleWrite($message);
    $action = json_decode( $message , true );

    //$this->game->action( $client, $action , $this->clients );
    if( $action['action'] == 'pressKey' ) $client->snake->setPressedKey( $action['key'] );
    else if( $action['action'] == 'newGame' ) $client->snake->newGame();
    else if( $action['action'] == 'nextMove' )
    {
      $clientsSendTo = [];
      $datas         = [];
      $cordinates    = [];
      foreach ($this->clients as $client)
      {
        if( $client->snake !== null && $client->snake->isPlaying )
        {
          $i = 0;
          $tiles = $this->game->nextMove( $client->snake );
          $datas[$client->getId()] = $tiles;

          if( $tiles['isPlaying'] )
          {
            foreach ($tiles['length'] as $tile)
            {
              if( isset($cordinates[$tile['x'].$tile['y']]) )
              {
                if( $i == 0 )
                {
                  $datas[$client->getId()] = [ 'isPlaying' => false ];
                  $client->snake->isPlaying = false;
                }
                if( $cordinates[$tile['x'].$tile['y']]['count'] == 0 )
                {
                  $datas[$cordinates[$tile['x'].$tile['y']]['id']] = [ 'isPlaying' => false ];
                  $this->clients[$cordinates[$tile['x'].$tile['y']]['id']]->snake->isPlaying = false;
                }
              }
              else $cordinates[$tile['x'].$tile['y']] = [ 'id' => $client->getId() , 'count' => $i++ ];
            }
          }

          $clientsSendTo[] = $client;
        }
      }
      //sending
      $dataStr = json_encode([ 'action' => 'move' , 'data' => $datas ]);
      foreach ($clientsSendTo as $client)
      {
        $this->send($client , $dataStr);
      }

    }

  }

  public function run()
  {
    $this->game->start();

    parent::run();
  }

  protected function onShutDownServer()
  {
    $this->game->stop();
  }

  protected function connecting($client)
  {
    //  after the instance of the User is created
  }
  
  protected function connected ($client)
  {
    $client->snake = new \Game\Snake( $this->game->getSnakeSize() , $client->getId() );

    $this->send( $client , json_encode([ 'action' => 'myId' , 'myId' => $client->getId() ]) );
  }
  
  protected function closed ($client)
  {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}