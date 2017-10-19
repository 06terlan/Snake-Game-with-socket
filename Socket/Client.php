<?php
/**
 * Simple server class which manage WebSocket protocols
 * @author Terlan Abdullayev
 * @license This program was created by A.Terlan . It is free
 * @version 1.0.0
 */

namespace WebSocket;

class Client {
	private $id;
	private $socket;
	private $handshake;
	private $pid;
	public 	$snake;

	public function __construct($id, $socket) {
		$this->id = $id;
		$this->socket = $socket;
		$this->handshake = false;
		$this->pid = null;
	}

	public function getId() {
		return $this->id;
	}

	public function getSocket() {
		return $this->socket;
	}

	public function getHandshake() {
		return $this->handshake;
	}

	public function getPid() {
		return $this->pid;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setSocket($socket) {
		$this->socket = $socket;
	}

	public function setHandshake($handshake) {
		$this->handshake = $handshake;
	}

	public function setPid($pid) {
		$this->pid = $pid;
	}
}
?>