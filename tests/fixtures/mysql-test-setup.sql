CREATE database IF NOT EXISTS test;

USE test;

delimiter $$

CREATE TABLE `slib_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column1` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Slib Test table.'$$

