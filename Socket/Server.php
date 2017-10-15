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
  protected function process ($user, $message)
  {
     // Do nothing: This runs after if any data comes from client
  }

  protected function connecting($user)
  {
    //  after the instance of the User is created
  }
  
  protected function connected ($user)
  {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
  }
  
  protected function closed ($user)
  {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}