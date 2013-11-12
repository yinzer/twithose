#twithose

Consume tweets from twitter in realtime

##composer
Make sure [composer](https://github.com/composer/composer) is installed and run:
```sh 
$ composer install
```

##database
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

##twitter oauth credentials

