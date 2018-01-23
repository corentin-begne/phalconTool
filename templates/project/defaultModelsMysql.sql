CREATE TABLE IF NOT EXISTS `GenderType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `GenderType` (`id`, `name`) VALUES
(1, 'male'),
(2, 'female');

CREATE TABLE IF NOT EXISTS `LangType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `LangType` (`id`, `name`) VALUES
(1, 'fr'),
(2, 'en');

CREATE TABLE IF NOT EXISTS `LangMessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_id_2` (`lang_id`,`name`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `SocialType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `SocialType` (`id`, `name`) VALUES
(1, 'googlePlus');

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `gender_id` int(11) NOT NULL,
  `social_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `social_id` (`social_id`),
  KEY `lang_id` (`lang_id`),
  KEY `gender_id` (`gender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `UserSocial` (
  `id` varchar(32) NOT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT '0',
  `token` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PermissionType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `PermissionType` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user'),
(3, 'anonymous');

CREATE TABLE IF NOT EXISTS `UserPermission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `LangMessage`
  ADD CONSTRAINT `lmid` FOREIGN KEY (`lang_id`) REFERENCES `LangType` (`id`) ON DELETE CASCADE;

ALTER TABLE `User`
  ADD CONSTRAINT `gtid` FOREIGN KEY (`gender_id`) REFERENCES `GenderType` (`id`),
  ADD CONSTRAINT `ltid` FOREIGN KEY (`lang_id`) REFERENCES `LangType` (`id`),
  ADD CONSTRAINT `stid` FOREIGN KEY (`social_id`) REFERENCES `SocialType` (`id`);

ALTER TABLE `UserPermission`
  ADD CONSTRAINT `UserPermission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `PermissionType` (`id`),
  ADD CONSTRAINT `UserPermission_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;