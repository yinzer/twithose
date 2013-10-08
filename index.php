<?php

require_once 'vendor/autoload.php';


// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "vd4d1M6fHJRjBFC2nZ4Dcg");
define("TWITTER_CONSUMER_SECRET", "1m5HJ3blPp8lPuVTZ6lMOX7KE0SNecGYbWfCDRg");

// The OAuth data for the twitter account
define("OAUTH_TOKEN", "167981712-RVQJw1gyhnXZpSZCuLmiTQa6mmPQJviDAyoqXLya");
define("OAUTH_SECRET", "vmm2bKKrxuoQWeVYbW3L4bMctq1GL2lDuueTwEveAyw");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('penspoints', 'pens points', '@penspoints', 'lthelp', '@lthelp','google'));
//$sc->consume();

var_dump($sc);