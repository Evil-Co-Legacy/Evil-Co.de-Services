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
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `code` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (languageID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (itemID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle 'language_item'
--

INSERT INTO language_item (itemID, languageID, name, value) VALUES(1, 1, 'bot.global.noSuchCommand', 'There is no such command.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(2, 2, 'bot.global.noSuchCommand', 'Unbekanntes Kommando.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(3, 1, 'command.join.success', 'Successfully joined the channel.');
INSERT INTO language_item (itemID, languageID, name, value) VALUES(4, 2, 'command.join.success', 'Channel erfolgreich betreten.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module'
--

CREATE TABLE module (
  moduleID int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  address varchar(255) COLLATE utf8_bin NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (moduleID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle 'module'
--

INSERT INTO module (moduleID, name, address, timestamp) VALUES(1, 'OpServ', 'OxF02D', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(2, 'CommandLoadModule', 'Ox52D96D20', 1);
INSERT INTO module (moduleID, name, address, timestamp) VALUES(3, 'CommandJoin', 'Ox45B196', 1);
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module_instance_bot'
--

CREATE TABLE module_instance_bot (
  instanceID int(10) unsigned NOT NULL AUTO_INCREMENT,
  moduleAddress varchar(255) COLLATE utf8_bin NOT NULL,
  `trigger` varchar(100) COLLATE utf8_bin NOT NULL,
  nick varchar(255) COLLATE utf8_bin NOT NULL,
  hostname text COLLATE utf8_bin NOT NULL,
  ident varchar(255) COLLATE utf8_bin NOT NULL,
  ip varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '127.0.0.1',
  modes varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '+Ik',
  gecos text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (instanceID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle 'module_instance_bot'
--

INSERT INTO module_instance_bot (instanceID, moduleAddress, trigger, nick, hostname, ident, ip, modes, gecos) VALUES(1, 'OxF02D', '?', 'OpServ', 'services.evil-co.de', 'services', '127.0.0.1', '+Ik', 'Oper Service');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle 'module_instance_command'
--

CREATE TABLE module_instance_command (
  instanceID int(10) unsigned NOT NULL AUTO_INCREMENT,
  address varchar(255) COLLATE utf8_bin NOT NULL,
  commandName varchar(255) COLLATE utf8_bin NOT NULL,
  appearInHelp tinyint(1) NOT NULL DEFAULT '1',
  parentAddress varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (instanceID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle 'module_instance_command'
--

INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(1, 'Ox52D96D20', 'LOADMODULE', 1, 'OxF02D');
INSERT INTO module_instance_command (instanceID, address, commandName, appearInHelp, parentAddress) VALUES(1, 'Ox45B196', 'JOIN', 1, 'OxF02D');

