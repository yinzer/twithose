<?php

require 'vendor/autoload.php';


$facebook = new Facebook(array(
	'appId' => '126519084063260',
	'secret' => '63d14bc45ae289cc3a86036bf722f716'
));

$q = "penspoints" ;

$search = $facebook->api('/search?q='.$q.'&type=post&limit=100');

// echo '<pre>';
// print_r($search);
// echo '</pre>';
// die;

$messages = array();

foreach ($search['data']  as $key => $value){
	$name = (isset($value['from']['name']))? $value['from']['name'] : "";
	$message = (isset($value['message']))? $value['message'] : "";
	$created_time = (isset($value['created_time']))? $value['created_time'] : "";
	$updated_time = (isset($value['updated_time']))? $value['updated_time'] : "";

	$likes = array();
	if (isset($value['likes']['data'])){
		foreach($value['likes']['data'] as $likedName){
			$likes[] = array(
				'fbid' => $likedName['id'],
				'name' => $likedName['name']
			);
		}
	}

	$comments = array();
	if (isset($value['comments']['data'])){
		foreach($value['comments']['data'] as $commentedName){
			$comments[] = array(
				'fbid' => $commentedName['id'],
				'name' => $commentedName['from']['name'],
				'message' => $commentedName['message']
			);
		}
	}
	//var_dump($likes);

	$messages[$value['id']] = array(
		'name' => $name,
		'message' => $message,
		'created_time' => $created_time,
		'updated_time' => $updated_time,
		'whoLiked' => $likes,
		'comments' => $comments
	);
	//$redis->
	unset($likes);
	unset($comments);
}


echo '<pre>';
print_r($messages);
echo '</pre>';

// foreach($messages as $key => $message){
// 	echo '<pre>';
// 	var_dump($message['whoLiked']);
// 	echo '</pre>';
// }
// die;

$redis = new Redis();
try {
	$redis->connect('127.0.0.1');
	//var_dump($redis->info());
	foreach ($messages as $key => $message){
		$redis->hMset('fb:'.$key, array(
			'name' => $message['name'],
			'message' => $message['message'],
			'created_time' => $message['created_time'],
			'updated_time' => $message['updated_time']
		));

		foreach ($message['whoLiked'] as $lKey => $whoLiked){
			$redis->hMset('fb:'.$key.':likes:'.$lKey, $whoLiked);
		}

		foreach ($message['comments'] as $cKey => $comments){
			$redis->hMset('fb:'.$key.':comments:'.$cKey, $comments);
		}
	}
} catch (Exception $e){
	echo $e->getMessage();
}
