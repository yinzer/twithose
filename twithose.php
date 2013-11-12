<?php
require_once 'vendor/autoload.php';

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "XXXXXXXX");
define("TWITTER_CONSUMER_SECRET", "XXXXXXXX");

// The OAuth data for the twitter account
define("OAUTH_TOKEN", "XXXXXXXX");
define("OAUTH_SECRET", "XXXXXXXX");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('google'));
$sc->consume();