<?php

class FilterTrackConsumer extends OauthPhirehose {

	private $redis;
    
    public function enqueueStatus($status){
		$this->redis = new Redis();
		$this->redis->pconnect('127.0.0.1');
		
		// var_dump($this->redis);
		// die;
        $data = json_decode($status, true);
        
        if (is_array($data) && isset($data['user']['screen_name'])) {
        	$arr = array(
	        	'id' => $data['id'],
	        	'sn' => $data['user']['screen_name'],
	        	'name' => $data['user']['name'],
	        	'avatar' => $data['user']['profile_image_url'],
	        	'txt' => urldecode($data['text']),
	        	'created' => $data['created_at'],
	        	'source' => strip_tags($data['source']),
	        	'timezone' => $data['user']['time_zone']
	        );

        	$this->redis->hMset($arr['sn'].':'.$arr['id'], $arr);
        	$this->redis->rPush('tweets', $arr['sn'].':'.$arr['id']);

        	// $this->redis->publish('tweet', $arr['sn'].':'.$arr['id']);
        	// $this->redis->publish('tweet-count', $this->redis->lLen('tweets'));
            
            print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
        }
    }
}