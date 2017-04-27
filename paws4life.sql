--
-- Database: `paws4life`
--
CREATE DATABASE IF NOT EXISTS `paws4life` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `paws4life`;

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--
-- 创建时间： 2017-03-23 18:49:42
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pets_id` int(32) UNSIGNED NOT NULL,
  `users_id` int(32) UNSIGNED NOT NULL,
  `buyers_id` int(32) UNSIGNED DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `buy_time` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orders_pets_users` (`pets_id`,`users_id`),
  KEY `orders_users` (`users_id`),
  KEY `orders_pets_buyers` (`buyers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 表的关联 `orders`:
--   `pets_id`
--       `pets` -> `id`
--   `buyers_id`
--       `users` -> `id`
--   `users_id`
--       `users` -> `id`
--

--
-- 转存表中的数据 `orders`
--

INSERT INTO `orders` (`id`, `pets_id`, `users_id`, `buyers_id`, `create_time`, `buy_time`, `deleted`) VALUES
(1, 1, 1, NULL, '2017-03-14 00:00:00', '0000-00-00 00:00:00', 0),
(2, 2, 1, NULL, '2017-03-14 00:00:00', '0000-00-00 00:00:00', 0),
(3, 3, 1, 36, '2017-03-16 00:00:00', '0000-00-00 00:00:00', 0),
(4, 4, 2, 3, '2017-03-16 00:00:00', '0000-00-00 00:00:00', 0),
(5, 9, 36, 3, '2017-03-23 20:05:41', '0000-00-00 00:00:00', 0),
(6, 10, 1, NULL, '2017-04-25 19:25:06', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- 表的结构 `pets`
--
-- 创建时间： 2017-04-24 16:50:11
--

DROP TABLE IF EXISTS `pets`;
CREATE TABLE IF NOT EXISTS `pets` (
  `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `title` varchar(72) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `gender` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `birthday` date DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `pets_name` (`name`) USING BTREE,
  KEY `pets_description` (`description`(72)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 表的关联 `pets`:
--

--
-- 转存表中的数据 `pets`
--

INSERT INTO `pets` (`id`, `name`, `title`, `description`, `image`, `type`, `gender`, `birthday`, `create_time`, `deleted`) VALUES
(1, 'testname', '', 'testdes', 'imgsa', '', '', NULL, '0000-00-00 00:00:00', 0),
(2, 'test1', '', '阿斯发达分萨芬', '支持vzcxzz', '', '', NULL, '0000-00-00 00:00:00', 0),
(3, 'test1', '', '阿斯发达分萨芬', '支持vzcxzz', '', '', NULL, '0000-00-00 00:00:00', 0),
(4, 'test1', '', '阿斯发达分萨芬', '支持vzcxzz', '', '', NULL, '0000-00-00 00:00:00', 0),
(5, '666', '', '', '', '', '', NULL, '2017-03-23 19:51:46', 0),
(6, '888', '', '', '', '', '', NULL, '2017-03-23 19:53:42', 0),
(7, '888', '', '', '', '', '', NULL, '2017-03-23 20:04:39', 0),
(8, '888', '', '', '', '', '', NULL, '2017-03-23 20:05:05', 0),
(9, '888', '', '', '', '', '', NULL, '2017-03-23 20:05:41', 0),
(10, '666', '', '777', '', '', '', '0000-00-00', '2017-04-25 19:25:06', 0);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--
-- 创建时间： 2017-04-27 14:41:49
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `phone` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `county` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `city` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `road` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_id` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `postcode` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `account` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `power` int(8) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_account` (`account`) USING BTREE,
  KEY `users_name` (`name`(3)),
  KEY `users_account_password` (`account`(3),`password`(3)),
  KEY `users_description` (`description`(72))
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 表的关联 `users`:
--

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `name`, `description`, `image`, `phone`, `county`, `city`, `road`, `address`, `user_id`, `postcode`, `account`, `password`, `power`, `deleted`) VALUES
(1, 'test', 'tttttttt', '', '', '', '', '', '', '', '', 'test', 'zxcv', 1, 0),
(2, 'asdf', 'zcvxzcvxzvc', '', '', '', '', '', '', '', '', 'asdf', '666', 1, 0),
(3, '8888', '阿凡达说法是否', '', '', '', '', '', '', '', '', '666666', '666666', 1, 0),
(4, '66666', '', '', '', '', '', '', '', '', '', 'aaa', 'bbbddd', 1, 0),
(32, '6666666', '', '', '', '', '', '', '', '', '', 'a', 'bbb', 1, 0),
(33, '', '', '', '', '', '', '', '', '', '', 'aa', 'bbb', 1, 0),
(36, '', '', '', '', '', '', '', '', '', '', 'abaa', 'bbb', 1, 0),
(39, '', '6666666', '', '', '', '', '', '', '', '', 'aababbaaaa', 'bbb', 1, 0),
(40, '', '', '', '', '', '', '', '', '', '', 'abbabbaaaa', 'bbb', 1, 0),
(42, '', '666677', '', '', '', '', '', '', '', '', 'abbbbb', 'bbb', 1, 0),
(46, '', '', '', '', '', '', '', '', '', '', 'abbbbbd', 'bbb', 1, 0),
(47, '', '', '', '', '', '', '', '', '', '', 'dda', 'bbb', 1, 0),
(48, '', '', '', '', '', '', '', '', '', '', 'dafdsa', 'bbbddd231313', 1, 1);

--
-- 限制导出的表
--

--
-- 限制表 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_pets` FOREIGN KEY (`pets_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `orders_pets_buyers` FOREIGN KEY (`buyers_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
