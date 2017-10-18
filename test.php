<?php

class ChildThread extends Threaded {
    public $data;

    public function run() {
      /* Do some work */

      $this->data = 'result';
    }
}