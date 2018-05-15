DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` ( `aid` int(11) NOT NULL AUTO_INCREMENT, `cid` int(11) DEFAULT NULL, `title` varchar(50) DEFAULT NULL, `cdate` int(11) DEFAULT NULL, `abstract` varchar(255) DEFAULT NULL, `comment_num` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`aid`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `article_content`;
CREATE TABLE `article_content` ( `id` int(11) NOT NULL AUTO_INCREMENT, `aid` int(11) DEFAULT NULL, `content` text, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` ( `cid` int(11) NOT NULL AUTO_INCREMENT, `key` varchar(10) DEFAULT NULL, `desc` varchar(50) DEFAULT NULL, `title` varchar(50) DEFAULT NULL, PRIMARY KEY (`cid`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` ( `id` int(11) NOT NULL AUTO_INCREMENT, `aid` int(11) DEFAULT NULL, `cdate` int(11) DEFAULT NULL, `author` varchar(20) DEFAULT NULL, `email` varchar(25) DEFAULT NULL, `website` varchar(100) DEFAULT NULL, `content` text, `parent` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` ( `id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(250) DEFAULT NULL, `avatar` varchar(250) DEFAULT NULL, `name` varchar(20) DEFAULT NULL, `desc` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` ( `id` int(11) NOT NULL AUTO_INCREMENT, `key` varchar(25) DEFAULT NULL, `val` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `social`;
CREATE TABLE `social` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(10) DEFAULT NULL, `icon` varchar(100) DEFAULT NULL, `url` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP VIEW IF EXISTS `article_list`;
CREATE VIEW `article_list` AS select `a`.`aid` AS `aid`,`a`.`cid` AS `cid`,`a`.`title` AS `title`,`a`.`cdate` AS `cdate`,`a`.`abstract` AS `abstract`,`a`.`comment_num` AS `comment_num`,`c`.`key` AS `key`,`c`.`title` AS `category`,`v`.`content` AS `content` from ((`article` `a` join `article_content` `v` on((`a`.`aid` = `v`.`aid`))) join `category` `c` on((`a`.`cid` = `c`.`cid`)));

DROP TRIGGER IF EXISTS `add_comment`;
CREATE TRIGGER `add_comment` AFTER INSERT ON `comment` FOR EACH ROW UPDATE `article` SET `comment_num` = `comment_num` + 1 WHERE `aid` = NEW.aid;

INSERT INTO `setting` VALUES (1,'site_name',"He110's Blog"),(2,'author','He110'),(3,'desc','手握日月摘星辰，世间无我这般人'),(4,'intro','言犹在耳凌云志，三尺丰碑丈五字；那日谁断我双翅，今朝可敢再来试？'),(5,'key','123456789');

INSERT INTO `link` VALUES (1,'http://blog.he110.info','https://avatars3.githubusercontent.com/u/16161582?s=460&v=4',"He110's Blog",'手握日月摘星辰，世间无我这般人。');
