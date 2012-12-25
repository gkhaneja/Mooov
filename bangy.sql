CREATE TABLE `bangalore_dst` (
  `row_id` int(10) unsigned NOT NULL,
  `col_id` int(10) unsigned NOT NULL,
  `users` varchar(500) DEFAULT NULL,
  KEY `row_id` (`row_id`,`col_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bangalore_src` (
  `row_id` int(10) unsigned NOT NULL,
  `col_id` int(10) unsigned NOT NULL,
  `users` varchar(500) DEFAULT NULL,
  KEY `row_id` (`row_id`,`col_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

