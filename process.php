<?php
require_once 'vendor/autoload.php';
// ini_set('default_socket_timeout', -1);
// date_default_timezone_set('America/New_York');
class Process {

	private $dbh;
	private $redis;

	public function __construct($dbhost, $dbname, $dbuser, $dbpass){
		$this->dbh = new DataBaseConnection($dbhost, $dbname, $dbuser, $dbpass);
		$this->redis = new Redis();
		$this->redis->pconnect('127.0.0.1');

		self::test();
		// self::subscribe();
	}

	private function subscribe(){
		$this->redis->subscribe(array('tweet-count','tweet'), array($this, 'f'));	
	}

	public function f($redis, $chan, $msg) {
		switch($chan) {
			case 'tweet':
				echo $msg . "\n";
				break;

			case 'tweet-count':
				if ($msg){
					self::test();
				}
				break;
		}
	}

	public function test(){
		$count = $this->redis->lLen('tweets');
		$tweets = $this->redis->lRange('tweets', 0, -1);
		if ($count){
			foreach ($tweets as $tweet){
				self::addTweetsToDB($tweet);
			}
		}
	}

	public function addTweetsToDB($id){
		$tweet = $this->redis->hGetAll($id);

		try {
			$query = "INSERT INTO test (name, screen_name, tweet_text, date_orig, tweet_date, tweet_id, source, timezone, avatar) VALUES (:name, :screen_name, :tweet_text, :date_orig, :tweet_date, :tweet_id, :source, :timezone, :avatar)";
			$this->dbh->prepare($query);

			$userTimezone = new DateTimeZone('America/New_York');
			$estTimezone = new DateTimeZone('EST');
			$date = new DateTime($tweet['created'], $estTimezone);
			$offset = $userTimezone->getOffset($date);
			$date->modify($offset . 'seconds');
			// echo $offset;
			$params = array(
				':name' => $tweet['name'],
				':screen_name' => $tweet['sn'],
				':tweet_text' => $tweet['txt'],
				':date_orig' => $tweet['created'],
				':tweet_date' => $date->format('Y-m-d H:i:s'),
				':tweet_id' => $tweet['id'],
				':source' => $tweet['source'],
				':timezone' => $tweet['timezone'],
				':avatar' => $tweet['avatar']
			);
			$this->dbh->exec($query, $params);
			$this->redis->lPop('tweets');
			$this->redis->setTimeout($tweet['sn'].':'.$tweet['id'], 1);
		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}

	public function __destruct(){}
}

$dbhost = "127.0.0.1";
$dbname = "tweets";
$dbuser = "root";
$dbpass = "asdf.1234!";

$process = new Process($dbhost, $dbname, $dbuser, $dbpass);