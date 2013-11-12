<?php
require_once 'vendor/autoload.php';

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "RZnKdFOJiaWPgTItYDzumQ");
define("TWITTER_CONSUMER_SECRET", "9yqD1Il4oyztxqefxbWSb090uHeLPWlm7vXPFMaZ4");

// The OAuth data for the twitter account
define("OAUTH_TOKEN", "167981712-8t3Rgo3SU2sraFrTlfNfhXLM6LFpCd715qSlDMij");
define("OAUTH_SECRET", "5ZS9O4WIKxyuMcxS8mp7A8v8wHk9N3lB0nkM40OPes");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('google'));
$sc->consume();