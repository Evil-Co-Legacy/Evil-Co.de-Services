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

CREATE TABLE module (
  moduleID int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  address varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (moduleID),
  UNIQUE KEY (address)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'module'
--

INSERT INTO module (moduleID, name, address, timestamp) VALUES(1, 'OpServ', 'OxF02D', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(2, 'CommandLoadModule', 'Ox52D96D20', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(3, 'CommandJoin', 'Ox45B196', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(4, 'CommandPart', 'Ox477B6E', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(5, 'AuthServ', 'Ox21A7E885', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(6, 'CommandAuth', 'Ox439D030291B', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(7, 'CommandShutdown', 'Ox1BAFEA1F', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(8, 'ChanServ', 'Ox1337', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(9, 'CommandRegister', 'OxAD47D1AF9', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(10, 'CommandPass', 'Ox18D46C95', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(11, 'CommandEmail', 'Ox11571D3EE', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(12, 'CommandMode', 'Ox23DD5B189BBAB2E', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(13, 'CommandBind', 'OxED6A84A0', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(14, 'CommandAccess', 'Ox25067B81', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(15, 'CommandCregister', 'OxB494EC43B6AA', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(16, 'CommandKick', 'Ox24AA79F6', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(17, 'CommandChangeuser', 'OxAD663DBFC', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(18, 'CommandListuser', 'Ox63D0FC', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(19, 'CommandCinfo', 'Ox71A35', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(20, 'KickRevengeExtension', 'Ox75A57AFDB160', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(21, 'CommandCunregister', 'Ox221D0E2529', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(22, 'CommandUnregister', 'Ox75A615CEDF20', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(23, 'JoinModeExtension', 'Ox75A637483520', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module_instance_bot'
--

CREATE TABLE module_instance_bot (
  instanceID int(10) unsigned NOT NULL AUTO_INCREMENT,
  moduleAddress varchar(255) NOT NULL,
  `trigger` varchar(100) NOT NULL,
  nick varchar(255) NOT NULL,
  hostname text NOT NULL,
  ident varchar(255) NOT NULL,
  ip varchar(255) NOT NULL DEFAULT '127.0.0.1',
  modes varchar(255) NOT NULL DEFAULT '+Ik',
  gecos text NOT NULL,
  PRIMARY KEY (instanceID),
  UNIQUE KEY (moduleAddress)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'module_instance_bot'
--

INSERT INTO module_instance_bot (instanceID, moduleAddress, `trigger`, nick, hostname, ident, ip, modes, gecos) VALUES(1, 'OxF02D', '?', 'OpServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Oper Service');
INSERT INTO module_instance_bot (instanceID, moduleAddress, `trigger`, nick, hostname, ident, ip, modes, gecos) VALUES(2, 'Ox21A7E885', '=', 'AuthServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Auth Service');
INSERT INTO module_instance_bot (instanceID, moduleAddress, `trigger`, nick, hostname, ident, ip, modes, gecos) VALUES(3, 'Ox1337', '!', 'ChanServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Channel Service');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module_instance_command'
--

CREATE TABLE module_instance_command (
  instanceID int(10) unsigned NOT NULL AUTO_INCREMENT,
  address varchar(255) NOT NULL,
  commandName varchar(255) NOT NULL,
  appearInHelp tinyint(1) NOT NULL DEFAULT '1',
  parentAddress varchar(255) NOT NULL,
  PRIMARY KEY (instanceID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'module_instance_command'
--

INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(1, 'Ox52D96D20', 'LOADMODULE', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(2, 'Ox45B196', 'JOIN', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(3, 'Ox477B6E', 'PART', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(4, 'Ox439D030291B', 'AUTH', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(5, 'Ox1BAFEA1F', 'SHUTDOWN', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(6, 'OxAD47D1AF9', 'REGISTER', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(7, 'Ox18D46C95', 'PASS', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(8, 'Ox11571D3EE', 'EMAIL', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(9, 'Ox45B196', 'JOIN', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(10, 'Ox23DD5B189BBAB2E', 'MODE', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(11, 'Ox45B196', 'JOIN', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(12, 'OxED6A84A0', 'BIND', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(13, 'Ox477B6E', 'PART', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(14, 'Ox477B6E', 'PART', 1, 'Ox21A7E885');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(15, 'Ox25067B81', 'ACCESS', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(16, 'OxB494EC43B6AA', 'REGISTER', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(17, 'Ox24AA79F6', 'KICK', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(18, 'OxAD663DBFC', 'CHANGEUSER', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(19, 'Ox63D0FC', 'LISTUSER', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(20, 'Ox71A35', 'INFO', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(21, 'Ox221D0E2529', 'UNREGISTER', 1, 'Ox1337');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(22, 'Ox75A615CEDF20', 'UNREGISTER', 1, 'Ox21A7E885');


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
