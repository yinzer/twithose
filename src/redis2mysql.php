<?php

class Process {

	private $dbh;
	private $redis;

	public function __construct($dbhost, $dbname, $dbuser, $dbpass){
		$this->dbh = new DataBaseConnection($dbhost, $dbname, $dbuser, $dbpass);
		$this->redis = new Redis();
		$this->redis->pconnect('127.0.0.1');
		self::checkList();
	}

	public function checkList(){
		$count = $this->redis->lLen('tweets');
		$tweets = $this->redis->lRange('tweets', 0, -1);
		if ($count > 10){
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