SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `int_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `backend` varchar(250) NOT NULL,
  `frontend` varchar(250) NOT NULL,
  `shortcut` tinyint(1) NOT NULL,
  `corder` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `int_admin` (`id`, `name`, `url`, `folder`, `backend`, `frontend`, `shortcut`, `corder`, `active`) VALUES
(1, 'Site', 'site', 'site', 'site/backend.php', 'site/frontend.php', 0, 1, 1),
(2, 'Login', 'login', 'users', 'users/backend.php', 'users/frontend.php', 1, 2, 1),
(3, 'Content', 'content', 'content', 'content/backend.php', 'content/frontend.php', 0, 3, 1);

CREATE TABLE IF NOT EXISTS `int_admin_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `int_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `int_charges` (`id`, `name`, `level`) VALUES
(1, 'Administrator', 0);

CREATE TABLE IF NOT EXISTS `int_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) NOT NULL,
  `title` varchar(250) NOT NULL,
  `code` text NOT NULL,
  `menu` tinyint(1) NOT NULL,
  `father` int(11) NOT NULL,
  `corder` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `int_content_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `title` varchar(250) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `int_information` (
  `title` varchar(255) NOT NULL,
  `footer` varchar(255) NOT NULL,
  `maintenance` tinyint(1) NOT NULL,
  `maintenancetext` varchar(255) NOT NULL,
  `register` tinyint(1) NOT NULL,
  PRIMARY KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `int_lang` (
  `lang` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `forced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lang`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `int_lang` (`lang`, `name`, `forced`) VALUES
('en', 'English', 0);

CREATE TABLE IF NOT EXISTS `int_metas` (
  `name` varchar(50) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `int_overwatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instant` int(11) NOT NULL,
  `device` varchar(50) NOT NULL,
  `browser` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `int_permissions` (
  `charge` int(11) NOT NULL,
  `module` int(11) NOT NULL,
  PRIMARY KEY (`charge`,`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `int_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folder` (`folder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `int_styles` (`id`, `name`, `folder`, `active`) VALUES
(1, 'JULIUS', 'julius', 1);

CREATE TABLE IF NOT EXISTS `int_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(250) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `valcode` varchar(250) NOT NULL,
  `activated` tinyint(1) NOT NULL,
  `password` text NOT NULL,
  `ip` varchar(50) NOT NULL,
  `session` text NOT NULL,
  `fingerprint` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`email`),
  UNIQUE KEY `valcode` (`valcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
