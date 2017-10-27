/**
 * Namespace
 */
var Game      = Game      || {};
var Keyboard  = Keyboard  || {};
var Sockets   = Sockets   || {};
var Component = Component || {};

/**
 * Keyboard Map
 */
Keyboard.Keymap = {
  37: 'left',
  38: 'up',
  39: 'right',
  40: 'down'
};
Sockets.Url = "ws://127.0.0.1:5555";
Sockets.WS = null;

/**
 * Keyboard Events
 */
Keyboard.ControllerEvents = function() {
  
  // Setts
  var self      = this;
  this.pressKey = null;
  this.key      = null;
  this.keymap   = Keyboard.Keymap;
  
  // Keydown Event
  document.onkeydown = function(event) {
    self.pressKey = event.which;
    self.key      = self.getKey();

    if( typeof(self.getKey()) != 'undefined' ) 
      Sockets.WS.send( JSON.stringify( {action : 'pressKey' , key : self.getKey() } ) );
  };
  
  // Get Key
  this.getKey = function() {
    return this.keymap[this.pressKey];
  };
};

/**
 * Game Component Stage
 */
Component.Stage = function(canvas) {

  this.width     = canvas.width;
  this.height    = canvas.height;
  this.length    = [];
  this.food      = {};
  this.score     = 0;
  this.direction = 'right';
  this.conf      = {
    cw   : 10,
    size : 5,
    fps  : 1000
  }
  
};

/**
 * Game Draw
 */
Game.Draw = function(context,canvas) {
  
  this.Com = new Component.Stage(canvas);

  // Draw Stage
  this.drawStage = function(data) {
    
    // Draw White Stage
		context.fillStyle = "white";console.log(data);
		context.fillRect(0, 0, this.Com.width, this.Com.height);
    
    for(var n in data)
    {
      if(n == 'food')
      {
        this.drawCell(data[n]['x'],data[n]['y'], 'rgb(45, 185, 57)' );
      }
      else if(data[n]['isPlaying'] == true)
      {
        for(var nn in data[n]['length'])
        {
          if(n==window.myId)
          {
            // Draw Cell
            this.drawCell(data[n]['length'][nn]['x'],data[n]['length'][nn]['y'], 'rgb(220, 73, 73)' );
            // Draw Score
            context.fillText('Your score: ' + data[n]['score'], 10, (this.Com.height - 5));
          }
          else this.drawCell(data[n]['length'][nn]['x'],data[n]['length'][nn]['y'], 'rgb(170, 170, 170)' );
        }
      }
      else if( n == window.myId )
      {
        alert('Game over');
      }
    }
  };

  // Draw Cell
  this.drawCell = function(x, y , color)
  {
    context.fillStyle = color;
    context.beginPath();
    context.arc((x * this.Com.conf.cw + 6), (y * this.Com.conf.cw + 6), 4, 0, 2*Math.PI, false);    
    context.fill();
  };

};


/**
 * Game Snake
 */
Game.Snake = function(elementId, conf)
{
  // Sets
  this.canvas   = document.getElementById(elementId);
  this.context  = this.canvas.getContext("2d");
  this.events   = new Keyboard.ControllerEvents();
};

function newGame()
{
  Sockets.WS.send( JSON.stringify( { action : 'newGame' } ) );
}

Sockets.Creator = function(url)
{
  Sockets.WS = new WebSocket(url);
  Sockets.WS.onopen = function(msg)
  {
    alert('Are you ready?'); 
    newGame();
  };
  Sockets.WS.onmessage = function(msg)
  {
    var dataT = JSON.parse(msg.data);

    if( dataT['action'] == 'move' ) window.draw.drawStage(dataT['data']);
    else if( dataT['action'] == 'myId' ) window.myId = dataT['myId'];
  };
  Sockets.WS.onclose = function(msg)
  {
    
  };
  Sockets.WS.onerror = function(event)
  {
    alert(event.data);
  };
}

/**
 * Window Load
 */
window.onload = function()
{
  var snake = new Game.Snake('stage');
  window.draw  = new Game.Draw(snake.context,snake.canvas);
  var socket = new Sockets.Creator(Sockets.Url);
};