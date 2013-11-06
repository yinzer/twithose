<?php

// ini_set('default_socket_timeout', -1);

class Publish {
	
	public $redis;

	public function __construct(){
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1');
	}

	public function pub($channel, $msg){
		$this->redis->publish($channel, $msg);
	}

	public function sub($channel){
		$this->redis->subscribe($channel, array($this, 'process'));
	}

	public function process(){
		echo 'process';
	}

	public function test($args){
		$this->redis->hMset('user:1', $args);
		echo '<pre>';
		print_r($args);
		echo '</pre>';
	}

	public function test2(){
		$args = $this->redis->hGetAll('user:1');
		var_dump($args);
	}

	public function __destruct(){
		$this->redis->close();
	}

}

$redis = new Publish();
// $redis->pub('chan-1', 'hello, world!');
// $redis->pub('chan-2', "super duper, that's nice!");
// $redis->sub(array('chan-1', 'chan-2'));

$arr = array(
	'id' => 1,
	'fname' => 'dave',
	'lname' => 'esaias',
	'email' => 'dge@davidesaias.com'
);

$redis->test2();
