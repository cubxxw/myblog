
CREATE TABLE IF NOT EXISTS `wp_wb_sst_broken_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) UNSIGNED DEFAULT '0',
  `check_date` datetime DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `memo` text,
  `url` varchar(256) DEFAULT NULL,
  `url_md5` varchar(32) DEFAULT NULL,
  `url_type` varchar(32) DEFAULT NULL,
  `url_text` text,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `check_date` (`check_date`),
  KEY `url_md5` (`url_md5`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB ;

