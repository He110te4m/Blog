# Host: localhost  (Version: 5.5.53)
# Date: 2018-02-14 12:46:23
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "article"
#

CREATE TABLE `article` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `content` blob COMMENT '内容',
  `cdate` int(11) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='文章表';

#
# Structure for table "category"
#

CREATE TABLE `category` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(10) DEFAULT NULL COMMENT '文章分类名',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='文章分类表';

#
# Structure for table "link"
#

CREATE TABLE `link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(15) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL COMMENT '友情链接地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='友情链接表';

#
# Structure for table "setting"
#

CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) DEFAULT NULL COMMENT '配置项名',
  `val` text COMMENT '配置项值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='设置表';

#
# Structure for table "social"
#

CREATE TABLE `social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` text COMMENT '图标',
  `url` text COMMENT '链接地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='社交链接表';
