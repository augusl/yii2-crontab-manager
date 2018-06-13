-- --------------------------------------------------------
-- 主机:                           127.0.0.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 cron_config 结构
CREATE TABLE IF NOT EXISTS `cron_config` (
  `cron_config_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '任务名称',
  `remark` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '任务备注',
  `status` tinyint(4) NOT NULL COMMENT '任务状态，0：正常运行，1：终止运行',
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '开始运行时间',
  `interval_time` int(11) NOT NULL DEFAULT '1' COMMENT '间隔时间',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0：间隔时间单位为秒，1：间隔时间单位为分钟，2：间隔时间单位为小时，3：间隔时间单位为天，4：间隔时间单位为周，5：间隔时间单位为月',
  `path` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '运行程序路径',
  `module` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '所属模块',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `last_run_time` timestamp NULL DEFAULT NULL COMMENT '末次执行时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`cron_config_id`),
  KEY `index_2` (`status`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='定时任务配置';

-- 正在导出表  cron_config 的数据：~2 rows (大约)
/*!40000 ALTER TABLE `cron_config` DISABLE KEYS */;
INSERT INTO `cron_config` (`cron_config_id`, `name`, `remark`, `status`, `start_time`, `interval_time`, `type`, `path`, `module`, `sort`, `last_run_time` , `create_time`,`update_time`) VALUES
	(1, '发送邮件', '我是测试的邮件群发任务', 0, '2018-05-28 11:53:19', 1, 1, 'email/send', 'Email', 1, '2018-05-28 11:53:19', '2018-05-28 11:53:19', '2018-05-28 11:53:19'),
	(2, '发送短信', '我是测试的短信群发任务', 0, '2018-05-28 11:53:19', 1, 1, 'email/send', 'Notic', 1, '2018-05-28 11:53:19', '2018-05-28 11:53:19', '2018-05-28 11:53:19');
/*!40000 ALTER TABLE `cron_config` ENABLE KEYS */;

-- 导出  表 cron_log 结构
CREATE TABLE IF NOT EXISTS `cron_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cron_config_id` int(11) NOT NULL COMMENT '定时任务配置ID',
  `status` tinyint(4) NOT NULL COMMENT '执行结果，0：正常执行，1：异常退出',
  `remark` varchar(555) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '备注原因',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '执行时间',
  PRIMARY KEY (`id`),
  KEY `fk_cron_config_zn_cron_log` (`cron_config_id`),
  CONSTRAINT `fk_cron_config_zn_cron_log` FOREIGN KEY (`cron_config_id`) REFERENCES `cron_config` (`cron_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='任务执行记录';

-- 正在导出表  cron_log 的数据：~1 rows (大约)
/*!40000 ALTER TABLE `cron_log` DISABLE KEYS */;
INSERT INTO `cron_log` (`id`, `cron_config_id`, `status`, `remark`, `create_time`) VALUES
	(1, 1, 0, '正常执行', '2018-05-28 11:55:33', '2018-05-28 11:55:33');
/*!40000 ALTER TABLE `cron_log` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
