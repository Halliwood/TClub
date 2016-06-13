-- -------------------------------------------------------- 
-- 
-- 表的结构 `user` 
-- 
DROP TABLE IF EXISTS `user`; 
CREATE TABLE IF NOT EXISTS `user` ( 
`id` int(10) unsigned auto_increment NOT NULL COMMENT '用户ID', 
`name` varchar(10) NOT NULL COMMENT '用户名', 
`passwd` char(32) NOT NULL COMMENT '登录密码MD5值', 
`status` int unsigned NOT NULL COMMENT '用户状态', 
`mail` varchar(40) NOT NULL COMMENT '邮箱', 
`phone` char(11) NOT NULL COMMENT '手机号', 
PRIMARY KEY (`id`), 
UNIQUE KEY `name` (`name`), 
UNIQUE KEY `mail` (`mail`), 
UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 COMMENT='用户表'; 

-- 
-- 转存表中的数据 `user` 
-- 
INSERT INTO `user` (`name`, `passwd`, `status`, `mail`, `phone`) VALUES 
('teppei', MD5('111111'), 1, 'teppei@fygame.com', '15118162760'); 