<?php

require_once 'vendor/autoload.php';

/**
 * Example of using Phirehose to display a live filtered stream using track words
 */
class FilterTrackConsumer extends OauthPhirehose
{

	private $redis;

    /**
    * Enqueue each status
    *
    * @param string $status
    */
    public function enqueueStatus($status)
    {
    	$this->redis = new Predis\Client();
    	//$this->redis->set('penspoints count', 0);
        /*
        * In this simple example, we will just display to STDOUT rather than enqueue.
        * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be  being enqueued and processed asyncronously from the collection process.
        */
        $data = json_decode($status, true);
        if (is_array($data) && isset($data['user']['screen_name'])) {
            // send to redis here...
            $this->redis->hset('penspoints', $data['id'], $data['user']['screen_name'].':::'.urldecode($data['text']).':::'.$data['created_at'].':::'.$data['user']['name']);
            $this->redis->incr('penspoints count');
            print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
        }
    }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "haEuzVBrxix2FN6FLD35g");
define("TWITTER_CONSUMER_SECRET", "DUsre4GQgMKrdXIanNhF1XeOJjP1Bll9GkEtJotaqg");

// The OAuth data for the twitter account
define("OAUTH_TOKEN", "167981712-RVQJw1gyhnXZpSZCuLmiTQa6mmPQJviDAyoqXLya");
define("OAUTH_SECRET", "vmm2bKKrxuoQWeVYbW3L4bMctq1GL2lDuueTwEveAyw");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('penspoints', 'pens points', '@penspoints', 'lthelp', '@lthelp','google'));
$sc->consume();

//var_dump($sc);