
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL, 
  `password` char(41) DEFAULT NULL,
  `is_admin` boolean default false,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET='utf8';

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
  `author` int(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `body` text,
  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `modified` TIMESTAMP DEFAULT `created`,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET='utf8';
