twithose
========
```SQL
CREATE TABLE `tweets` (
  `name` varchar(50) DEFAULT NULL,
  `screen_name` varchar(15) DEFAULT '',
  `tweet_text` varchar(200) DEFAULT '',
  `tweet_date` varchar(30) DEFAULT '',
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tweet_id` bigint(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2274 DEFAULT CHARSET=utf8;
```
