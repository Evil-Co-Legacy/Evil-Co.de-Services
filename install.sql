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
-- Tabellenstruktur für Tabelle 'language_item'
--

CREATE TABLE language_item (
  itemID int(10) unsigned NOT NULL AUTO_INCREMENT,
  languageID int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (itemID)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle 'language_item'
--

INSERT INTO language_item (itemID, languageID, name, value) VALUES(1, 1, 'bot.global.noSuchCommand', 'There is no such command.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(2, 2, 'bot.global.noSuchCommand', 'Unbekanntes Kommando.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(3, 1, 'command.join.success', 'Successfully joined the channel "%s".');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(4, 2, 'command.join.success', 'Channel "%s" erfolgreich betreten.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(5, 1, 'command.join.syntaxHint', 'join <channelname>');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(6, 2, 'command.join.syntaxHint', 'join <channelname>');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(7, 1, 'command.part.success', 'Successfully parted the channel "%s".');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(8, 2, 'command.part.success', 'Channel "%s" erfolgreich verlassen.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(9, 1, 'command.part.syntaxHint', 'part <channelname>');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(10, 2, 'command.part.syntaxHint', 'part <channelname>');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(11, 1, 'command.auth.success', 'Successfully authed to "%s".');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(12, 2, 'command.auth.success', 'Erfolgreich als "%s" angemeldet.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(13, 1, 'command.auth.invalidCredentials', 'The provided credentials were not correct');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(14, 2, 'command.auth.invalidCredentials', 'Fehlerhafte Daten.');



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
  PRIMARY KEY (instanceID),
  UNIQUE KEY (address)
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
INSERT INTO authserv_users (userID, accountname, password, salt, email, accessLevel, active) VALUES (2, 'Akkarin', 'd0caabc3b9c1b57c181f6b1955abd2827e6b7e18', '9415c250e2bcf1670819cf6e0063f2d4c768973a', 'akkarin@***', 1000, 1);

CREATE TABLE chanserv_channels (
	channel varchar(255) NOT NULL,
	modes varchar(255) NOT NULL,
	PRIMARY KEY (channel)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO chanserv_channels (channel, modes) VALUES ('#Server', '+AOPpnt');
