<?php
require_once 'vendor/autoload.php';

$redis = new Predis\Client();

$count = count($redis->hgetall('penspoints'));
$tweets = array();
if ($count > 300){
	$raw = $redis->hgetall('penspoints');
	foreach ($raw as $key => $data){
		$data = preg_split("/:::/", $data);
		$tweets[] = array(
			'tweet_id' => $key,
			'screen_name' => $data[0],
			'text' => utf8_encode($data[1]),
			'date_created' => $data[2],
			'name' => $data[3]
		);
	}
}

var_dump($tweets);