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
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `social_id` (`social_id`),
  KEY `lang_id` (`lang_id`),
  KEY `gender_id` (`gender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `UserSocial` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT '0',
  `token` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `User`
  ADD CONSTRAINT `gtid` FOREIGN KEY (`gender_id`) REFERENCES `GenderType` (`id`),
  ADD CONSTRAINT `ltid` FOREIGN KEY (`lang_id`) REFERENCES `LangType` (`id`),
  ADD CONSTRAINT `stid` FOREIGN KEY (`social_id`) REFERENCES `SocialType` (`id`);

ALTER TABLE `UserSocial`
  ADD CONSTRAINT `uid` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE;