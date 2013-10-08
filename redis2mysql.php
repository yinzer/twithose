<?php
require_once 'vendor/autoload.php';

class redis2mysql {

	private $dbh;
	private $redis;
	private $client;
	private $pubsub;

	public function __construct($dbhost, $dbname, $dbuser, $dbpass){
		$this->dbh = new DataBaseConnection($dbhost, $dbname, $dbuser, $dbpass);
		$this->redis = new Predis\Client();

		$single_server = array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 15
		);
		$this->client = new Predis\Client($single_server + array('read_write_timeout' => 0));

		//self::countTweets();
		self::subscribe();
	}

	private function subscribe(){
		$this->pubsub = $this->client->pubSub();
		$this->pubsub->subscribe('control_channel','notifications');

		foreach($this->pubsub as $message){
			switch ($message->kind) {
				case 'subscribe':
					echo "subscribed to {$message->channel}\n";
					break;
				case 'message':
					if ($message->channel == 'control_channel'){
						if ($message->payload == 'quit_loop'){
							echo "Aborting pubsub loop...\n";
							$this->pubsub->unsubscribe();
						} else {
							echo "Received an unrecognized command: {$message->payload}.\n";
						}
					} else {
						echo "Received the following message from {$message->channel}:\n",
								"	{$message->payload}\n\n";
					}
					break;
			}
		}
	}

	private function countTweets(){
		if ($this->redis->get('penspoints count') > 300){
			self::addTweetsToDB();
		}
	}

	private function addTweetsToDB(){
		$raw = $this->redis->hgetall('penspoints');
		try {
			$query = "INSERT INTO penspoints (tweet_id, screen_name, tweet_text, tweet_date, name) VALUES (:tweet_id, :screen_name, :tweet_text, :tweet_date, :name)";
			$this->dbh->prepare($query);

			foreach ($raw as $key => $data){
				$data = preg_split("/:::/", $data);
				$params = array(
					':tweet_id' => $key,
					':screen_name' => $data[0],
					':tweet_text' => $data[1],
					':tweet_date' => $data[2],
					':name' => $data[3]
				);
				$this->dbh->exec($query, $params);
			}
			$this->redis->flushdb();
			echo "success";	
		} catch (PDOException $e){
			echo $e->getMessage();
		}
	}

	public function __destruct(){
		unset($this->pubsub);
	}
}

$dbhost = "127.0.0.1";
$dbname = "tweets";
$dbuser = "";
$dbpass = "";

$m = new redis2mysql($dbhost, $dbname, $dbuser, $dbpass);
