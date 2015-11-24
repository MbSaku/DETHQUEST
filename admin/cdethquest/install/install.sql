
CREATE TABLE IF NOT EXISTS `cdeth_characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `class` int(11) NOT NULL,
  `race` int(11) NOT NULL,
  `body` int(11) NOT NULL,
  `hair` int(11) NOT NULL,
  `head` int(11) NOT NULL,
  `face` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `level` int(11) NOT NULL,
  `pc` tinyint(1) NOT NULL,
  `health` int(11) NOT NULL,
  `maxhealth` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `dexterity` int(11) NOT NULL,
  `constitution` int(11) NOT NULL,
  `intelligence` int(11) NOT NULL,
  `coins` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `deaths` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_character_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playercharacter` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `item` int(11) NOT NULL,
  `equipped` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `cdeth_charges` (`id`, `name`, `level`) VALUES
(1, 'Webmaster', 1);

CREATE TABLE IF NOT EXISTS `cdeth_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scenario` int(11) NOT NULL,
  `faction` int(11) NOT NULL,
  `instant` int(11) NOT NULL,
  `player` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `playable` tinyint(1) NOT NULL,
  `health` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `dexterity` int(11) NOT NULL,
  `constitution` int(11) NOT NULL,
  `intelligence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_classes_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_class_items` (
  `class` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `item` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`class`,`type`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_game` (
  `pchar` int(11) NOT NULL,
  `scenario` int(11) NOT NULL,
  `fow` text NOT NULL,
  `actions` int(11) NOT NULL DEFAULT '0',
  `pjorder` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `instant` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pchar`,`scenario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_item_armor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `protection` int(11) NOT NULL,
  `hitpoints` int(11) NOT NULL,
  `hashelmet` tinyint(1) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL,
  `premium` int(11) NOT NULL,
  `forsale` tinyint(1) NOT NULL,
  `icon` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_item_equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `premium` int(11) NOT NULL,
  `icon` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  `permanent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_item_healing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `premium` int(11) NOT NULL,
  `health` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_item_repairing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `premium` int(11) NOT NULL,
  `armor` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_item_trad` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`,`type`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_item_weapon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `close` tinyint(1) NOT NULL,
  `attacks` int(11) NOT NULL,
  `atrange` int(11) NOT NULL,
  `impact` int(11) NOT NULL,
  `damage` int(11) NOT NULL,
  `piercing` int(11) NOT NULL,
  `clipsize` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `premium` int(11) NOT NULL,
  `forsale` tinyint(1) NOT NULL,
  `icon` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  `hands` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place` int(11) NOT NULL,
  `playable` tinyint(1) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `level` text NOT NULL,
  `graph` text NOT NULL,
  `weather` text NOT NULL,
  `doors` text NOT NULL,
  `sprites` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_maps_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_permissions` (
  `charge` int(11) NOT NULL,
  `access` varchar(50) NOT NULL,
  PRIMARY KEY (`charge`,`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cdeth_permissions` (`charge`, `access`) VALUES
(1, 'administration'),
(1, 'charges'),
(1, 'permissions');

CREATE TABLE IF NOT EXISTS `cdeth_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_places_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_races` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `playable` tinyint(1) NOT NULL,
  `hands` int(11) NOT NULL,
  `armor` tinyint(1) NOT NULL,
  `xscaling` double NOT NULL,
  `yscaling` double NOT NULL,
  `icon` varchar(250) NOT NULL,
  `modhealth` int(11) NOT NULL,
  `modspeed` int(11) NOT NULL,
  `modstrength` int(11) NOT NULL,
  `moddexterity` int(11) NOT NULL,
  `modconstitution` int(11) NOT NULL,
  `modintelligence` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_races_trad` (
  `id` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_race_body` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_race_class` (
  `race` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  PRIMARY KEY (`race`,`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_race_dialog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `quote` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_race_face` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_race_hair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_race_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `maleimage` varchar(250) NOT NULL,
  `femaleimage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_race_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_scenario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map` int(11) NOT NULL,
  `started` int(11) NOT NULL,
  `faction` int(11) NOT NULL,
  `players` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `turntime` int(11) NOT NULL,
  `level` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_scenario_entity` (
  `scenario` int(11) NOT NULL,
  `coordx` int(11) NOT NULL,
  `coordy` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `entity` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  PRIMARY KEY (`scenario`,`coordx`,`coordy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cdeth_squares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cdeth_user` (
  `user` int(11) NOT NULL,
  `charge` int(11) NOT NULL,
  `playercharacter` int(11) NOT NULL,
  `fingerprint` text NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

