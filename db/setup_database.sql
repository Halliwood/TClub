-- -------------------------------------------------------- 
-- 
-- 表的结构 `user` 
-- 
DROP TABLE IF EXISTS `user`; 
CREATE TABLE IF NOT EXISTS `user` ( 
`id` int(10) unsigned auto_increment NOT NULL COMMENT '用户ID', 
`name` varchar(10) NOT NULL COMMENT '用户名', 
`passwd` varchar(32) NOT NULL COMMENT '登录密码MD5值', 
`identifier` varchar(32) COMMENT '第二身份标识', 
`token` varchar(32) COMMENT '永久登录标识', 
`timeout` int(10) unsigned COMMENT '永久登录超时时间', 
`status` int unsigned NOT NULL COMMENT '用户状态', 
`mail` varchar(40) NOT NULL COMMENT '邮箱', 
`phone` char(11) NOT NULL COMMENT '手机号', 
`coin` int(10) NOT NULL COMMENT '金币', 
PRIMARY KEY (`id`), 
UNIQUE KEY `name` (`name`), 
UNIQUE KEY `mail` (`mail`), 
UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='用户表'; 

-- 
-- 转存表中的数据 `user` 
-- 
INSERT INTO `user` (`name`, `passwd`, `status`, `mail`, `phone`, `coin`) VALUES 
('teppei', MD5('111111'), 2, 'teppei@fygame.com', '15118162760', '8888'); 


-- -------------------------------------------------------- 
-- 
-- 表的结构 `preorder` 
-- 
DROP TABLE IF EXISTS `preorder`; 
CREATE TABLE IF NOT EXISTS `preorder` ( 
`id` int(10) unsigned auto_increment NOT NULL COMMENT '预约ID', 
`userid` int(10) NOT NULL COMMENT '预约用户ID', 
`builder` int unsigned NOT NULL COMMENT '预约机器', 
`actionTime` datetime NOT NULL COMMENT '预约时间',  
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='构建机预约表'; 