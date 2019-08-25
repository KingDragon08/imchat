CREATE SCHEMA `imchat` DEFAULT CHARACTER SET utf8mb4 ;

CREATE TABLE `addfriends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `message` varchar(200) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0->请求加好友,1->同意加好友,2->拒绝加好友',
  `timestamp` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(200) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `txt` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0->不展示，1->展示',
  `timestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `ext` varchar(200) DEFAULT NULL,
  `timestamp` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT '0->单发红包，1->群发红包，2->转账',
  `number` int(11) NOT NULL DEFAULT '0',
  `joiner` varchar(2000) NOT NULL DEFAULT '[]',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0->未领取，1->已领取',
  `opened` int(11) NOT NULL DEFAULT '0',
  `openTime` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cfg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(45) NOT NULL,
  `value` varchar(10000) DEFAULT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `chatrooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomId` varchar(200) NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `owner` varchar(45) NOT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  `updated_at` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT '聊天室类型，如牛牛',
  `cfg` text,
  `rules` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `momentsId` int(11) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `timestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `game_niuniu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomId` varchar(100) NOT NULL,
  `bonusId` int(11) DEFAULT NULL COMMENT '对应的红包id',
  `banker` varchar(100) NOT NULL COMMENT '庄家id',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态，0->进行中,1->已结束',
  `joiners` varchar(1000) DEFAULT NULL COMMENT '参与者json字符串,username',
  `timestamp` varchar(45) DEFAULT NULL COMMENT '时间戳',
  `result` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `img` varchar(200) NOT NULL,
  `type` varchar(45) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` varchar(200) NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `owner` int(11) NOT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  `updated_at` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT '群组类型，如->niuniu',
  `cfg` text COMMENT '游戏群的配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `content` varchar(1000) NOT NULL,
  `imgs` varchar(4000) DEFAULT '图片逗号分隔字符串',
  `timestamp` varchar(45) NOT NULL,
  `video` varchar(500) DEFAULT NULL COMMENT '视频缩略图;视频地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `thumb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `momentsId` int(11) NOT NULL,
  `timestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(200) DEFAULT NULL,
  `username` varchar(200) NOT NULL,
  `avatar` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `jifen` int(11) NOT NULL DEFAULT '0',
  `bonus` int(11) NOT NULL DEFAULT '0',
  `created_at` varchar(20) NOT NULL,
  `updated_at` varchar(20) DEFAULT NULL,
  `pyqImg` varchar(200) NOT NULL DEFAULT 'http://172.22.222.89/uploads/pyq.jpeg',
  `sign` varchar(200) NOT NULL DEFAULT '无个性不签名' COMMENT '签名',
  `agent` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `shoukuanma` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `imchat`.`admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `role` VARCHAR(45) NOT NULL DEFAULT 'agent' COMMENT 'Agent or admin',
  `created_time` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `imchat`.`charge` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `userId` INT NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `timestamp` VARCHAR(45) NOT NULL,
  `status` INT NOT NULL DEFAULT 0 COMMENT '处理状态，0->上传，1->已通过，2->已拒绝',
  PRIMARY KEY (`id`));

INSERT INTO `imchat`.`admin` (`username`, `password`, `created_time`, `role`) VALUES ('admin', 'e10adc3949ba59abbe56e057f20f883e', '1562340091', 'admin');


