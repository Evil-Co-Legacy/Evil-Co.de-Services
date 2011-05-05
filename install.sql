-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 27. Dezember 2010 um 11:21
-- Server Version: 5.1.41
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: 'evilcode_services'
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'language'
--

CREATE TABLE `language` (
  languageID int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (languageID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'language'
--

INSERT INTO language (languageID, name, code) VALUES(1, 'English', 'en');
INSERT INTO language (languageID, name, code) VALUES(2, 'Deutsch', 'de');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module'
--

CREATE TABLE module_instance (
  moduleID int(10) unsigned NOT NULL AUTO_INCREMENT,
  moduleName varchar(255) NOT NULL,
  PRIMARY KEY (moduleID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'module'
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module_instance_bot'
--

CREATE TABLE module_instance_bot (
  instanceID int(10) unsigned NOT NULL AUTO_INCREMENT,
  moduleName varchar(255) NOT NULL,
  publicTrigger varchar(100) NOT NULL,
  nickname varchar(255) NOT NULL,
  hostname text NOT NULL,
  ident varchar(255) NOT NULL,
  ip varchar(255) NOT NULL DEFAULT '127.0.0.1',
  modes varchar(255) NOT NULL DEFAULT '+Ik',
  gecos text NOT NULL,
  PRIMARY KEY (instanceID),
  UNIQUE KEY (moduleName)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'module_instance_bot'
--

INSERT INTO
	module_instance_bot (instanceID, moduleName, publicTrigger, nickname, hostname, ident, ip, modes, gecos)
VALUES
	(1, 'OpServ', '?', 'OpServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Oper Service'),
	(2, 'AuthServ', '=', 'AuthServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Auth Service'),
	(3, 'ChanServ', '!', 'ChanServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Channel Service');

-- --------------------------------------------------------


CREATE TABLE authserv_users (
	userID int(10) unsigned NOT NULL AUTO_INCREMENT,
	accountname varchar(255) NOT NULL,
	password char(40) NOT NULL,
	salt char(40) NOT NULL,
	email varchar(255) NOT NULL,
	accessLevel int(10) NOT NULL DEFAULT 0,
	active tinyint(1) NOT NULL DEFAULT 0,
	time int(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (userID),
	UNIQUE KEY (accountname)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO authserv_users (userID, accountname, password, salt, email, accessLevel, active) VALUES (1, 'TimWolla', '9ebc2a357640ec74025a7f5ee4259a6060a6daf5', '9415c250e2bcf1670819cf6e0063f2d4c768973a', 'timwolla@***', 1000, 1);
INSERT INTO authserv_users (userID, accountname, password, salt, email, accessLevel, active) VALUES (2, 'Akkarin', '9ebc2a357640ec74025a7f5ee4259a6060a6daf5', '9415c250e2bcf1670819cf6e0063f2d4c768973a', 'akkarin@***', 1000, 1);

CREATE TABLE chanserv_channels (
	channel varchar(255) NOT NULL,
	modes varchar(255) NOT NULL,
	time int(10) NOT NULL DEFAULT 0,
	registrar int(10) NOT NULL DEFAULT 0,
	unregistercode char(40) NOT NULL DEFAULT '',
	defaultTopic text NOT NULL,
	PRIMARY KEY (channel)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE chanserv_channels_to_users (
	channel varchar(255) NOT NULL,
	userID int(10) unsigned NOT NULL,
	accessLevel int(10) NOT NULL,
	PRIMARY KEY (channel, userID),
	KEY (userID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE chanserv_channel_accessLevel (
	channel varchar(255) NOT NULL,
	function varchar(255) NOT NULL,
	accessLevel int(10) NOT NULL,
	PRIMARY KEY (channel, function),
	KEY (function)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE chanserv_default_accessLevel (
	function varchar(255) NOT NULL,
	accessLevel int(10) NOT NULL,
	PRIMARY KEY (function)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('access', 	500); -- Completely done, you can add / delete users and change needed access
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('forceMode', 	400);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('giveOp', 	300);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('mode', 	300); -- Half done, !mode works, but he can not check /mode
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('permban', 	200);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('ban', 	150);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('getOp', 	100);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('topic', 	100);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('kick', 	100); -- Almost done, !kick works and he can check manual kicks now, but it should be possible to kick via hostmask / ident
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('giveVoice', 	100);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('invite', 	 50);
INSERT INTO chanserv_default_accessLevel (function, accessLevel) VALUES ('getVoice', 	 10);
