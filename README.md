#twithose

Consume tweets from twitter in realtime

##composer
Install [Composer](https://github.com/composer/composer) Globally:
```sh
$ curl -sS https://getcomposer.org/installer | php
$ mv composer.phar /usr/local/bin/composer
```

```sh 
$ composer install
```

##redis
Install:
* [redis.io](http://redis.io/)
* [nicolasff/phpredis](https://github.com/nicolasff/phpredis)


##database
Setup table for tweets
```SQL
CREATE TABLE `tweets` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `screen_name` varchar(15) DEFAULT '',
  `tweet_text` varchar(200) DEFAULT '',
  `date_orig` varchar(30) DEFAULT NULL,
  `tweet_date` datetime DEFAULT NULL,
  `tweet_id` bigint(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2098 DEFAULT CHARSET=utf8;
```
Add your database connection information to [process.php](process.php)

##twitter oauth credentials
You will need the following from [Twitter](https://dev.twitter.com):
* TWITTER_CONSUMER_KEY
* TWITTER_CONSUMER_SECRET
* OAUTH_TOKEN
* OAUTH_SECRET
Once you have these, replace them accordingly in [twithose.php](twithose.php)

#run
* Run twithose.php as a process
* Setup a cronjob to run process.php however often you'd like (try 5 minutes?)
