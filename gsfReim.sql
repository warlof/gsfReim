-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: reimbursement
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adminSettings`
--

DROP TABLE IF EXISTS `adminSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminSettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannedUsers`
--

DROP TABLE IF EXISTS `bannedUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bannedUsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `banStart` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banEnd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `banStart` (`banStart`),
  KEY `banEnd` (`banEnd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invMarketGroups`
--

DROP TABLE IF EXISTS `invMarketGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invMarketGroups` (
  `marketGroupID` int(11) NOT NULL,
  `parentGroupID` int(11) DEFAULT NULL,
  `marketGroupName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iconID` int(11) DEFAULT NULL,
  `hasTypes` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`marketGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invTypes`
--

DROP TABLE IF EXISTS `invTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invTypes` (
  `typeID` int(11) NOT NULL,
  `groupID` int(11) DEFAULT NULL,
  `typeName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mass` double DEFAULT NULL,
  `volume` double DEFAULT NULL,
  `capacity` double DEFAULT NULL,
  `portionSize` int(11) DEFAULT NULL,
  `raceID` tinyint(3) unsigned DEFAULT NULL,
  `basePrice` decimal(19,4) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `marketGroupID` int(11) DEFAULT NULL,
  `chanceOfDuplicating` double DEFAULT NULL,
  PRIMARY KEY (`typeID`),
  KEY `invTypes_IX_Group` (`groupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kills`
--

DROP TABLE IF EXISTS `kills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kills` (
  `killID` bigint(20) NOT NULL,
  `killTime` datetime NOT NULL,
  `crest_link` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `bcast` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `fit` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `victimName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `victimID` bigint(20) NOT NULL,
  `corpID` bigint(20) NOT NULL,
  `corpName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sysID` bigint(20) NOT NULL,
  `sysName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `regID` bigint(20) NOT NULL,
  `regName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `secStatus` decimal(10,2) NOT NULL,
  `availablePayouts` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `ptQualified` int(11) NOT NULL,
  `overPtCap` int(11) NOT NULL,
  `shipID` bigint(20) NOT NULL,
  `shipName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `reservedBy` mediumtext COLLATE utf8_unicode_ci,
  `reservedDate` datetime DEFAULT NULL,
  `submittedBy` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `paid` int(11) NOT NULL DEFAULT '0' COMMENT '0 = NOT PROCESSED, 1 = PAID, 2 = DENIED',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attackers` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`killID`),
  KEY `paid` (`paid`),
  KEY `regID` (`regID`),
  KEY `shipID` (`shipID`),
  KEY `paid_2` (`paid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapConstellations`
--

DROP TABLE IF EXISTS `mapConstellations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mapConstellations` (
  `regionID` int(11) DEFAULT NULL,
  `constellationID` int(11) NOT NULL,
  `constellationName` longtext COLLATE utf8_unicode_ci,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `z` double DEFAULT NULL,
  `xMin` double DEFAULT NULL,
  `xMax` double DEFAULT NULL,
  `yMin` double DEFAULT NULL,
  `yMax` double DEFAULT NULL,
  `zMin` double DEFAULT NULL,
  `zMax` double DEFAULT NULL,
  `factionID` int(11) DEFAULT NULL,
  `radius` double DEFAULT NULL,
  PRIMARY KEY (`constellationID`),
  KEY `mapConstellations_IX_region` (`regionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapRegions`
--

DROP TABLE IF EXISTS `mapRegions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mapRegions` (
  `regionID` int(11) NOT NULL,
  `regionName` longtext COLLATE utf8_unicode_ci,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `z` double DEFAULT NULL,
  `xMin` double DEFAULT NULL,
  `xMax` double DEFAULT NULL,
  `yMin` double DEFAULT NULL,
  `yMax` double DEFAULT NULL,
  `zMin` double DEFAULT NULL,
  `zMax` double DEFAULT NULL,
  `factionID` int(11) DEFAULT NULL,
  `radius` double DEFAULT NULL,
  PRIMARY KEY (`regionID`),
  KEY `mapRegions_IX_region` (`regionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapSolarSystems`
--

DROP TABLE IF EXISTS `mapSolarSystems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mapSolarSystems` (
  `regionID` int(11) DEFAULT NULL,
  `constellationID` int(11) DEFAULT NULL,
  `solarSystemID` int(11) NOT NULL,
  `solarSystemName` longtext COLLATE utf8_unicode_ci,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `z` double DEFAULT NULL,
  `xMin` double DEFAULT NULL,
  `xMax` double DEFAULT NULL,
  `yMin` double DEFAULT NULL,
  `yMax` double DEFAULT NULL,
  `zMin` double DEFAULT NULL,
  `zMax` double DEFAULT NULL,
  `luminosity` double DEFAULT NULL,
  `border` tinyint(4) DEFAULT NULL,
  `fringe` tinyint(4) DEFAULT NULL,
  `corridor` tinyint(4) DEFAULT NULL,
  `hub` tinyint(4) DEFAULT NULL,
  `international` tinyint(4) DEFAULT NULL,
  `regional` tinyint(4) DEFAULT NULL,
  `constellation` tinyint(4) DEFAULT NULL,
  `security` double DEFAULT NULL,
  `factionID` int(11) DEFAULT NULL,
  `radius` double DEFAULT NULL,
  `sunTypeID` int(11) DEFAULT NULL,
  `securityClass` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`solarSystemID`),
  KEY `mapSolarSystems_IX_region` (`regionID`),
  KEY `mapSolarSystems_IX_constellation` (`constellationID`),
  KEY `mapSolarSystems_IX_security` (`security`),
  KEY `mss_name` (`solarSystemName`(40))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentsCompleted`
--

DROP TABLE IF EXISTS `paymentsCompleted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentsCompleted` (
  `killID` bigint(20) NOT NULL,
  `paidBy` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `payoutType` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `payoutAmount` decimal(15,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payoutNotes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`killID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentsDenied`
--

DROP TABLE IF EXISTS `paymentsDenied`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentsDenied` (
  `killID` bigint(20) NOT NULL,
  `reason` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `deniedBy` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`killID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payoutTypes`
--

DROP TABLE IF EXISTS `payoutTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payoutTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `prefix` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payouts`
--

DROP TABLE IF EXISTS `payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeID` bigint(20) NOT NULL,
  `typeName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `payoutType` int(11) NOT NULL,
  `payoutAmount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payoutType` (`payoutType`),
  KEY `typeID` (`typeID`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ptRegions`
--

DROP TABLE IF EXISTS `ptRegions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ptRegions` (
  `regID` int(11) NOT NULL,
  `regName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`regID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ulog`
--

DROP TABLE IF EXISTS `ulog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ulog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `refid` bigint(20) DEFAULT NULL,
  `taskid` int(11) DEFAULT NULL,
  `eventtimedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `taskid` (`taskid`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `vwdeniedPayments`
--

DROP TABLE IF EXISTS `vwdeniedPayments`;
/*!50001 DROP VIEW IF EXISTS `vwdeniedPayments`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwdeniedPayments` (
  `reason` tinyint NOT NULL,
  `deniedOn` tinyint NOT NULL,
  `killID` tinyint NOT NULL,
  `killTime` tinyint NOT NULL,
  `crest_link` tinyint NOT NULL,
  `bcast` tinyint NOT NULL,
  `fit` tinyint NOT NULL,
  `victimName` tinyint NOT NULL,
  `victimID` tinyint NOT NULL,
  `corpID` tinyint NOT NULL,
  `corpName` tinyint NOT NULL,
  `sysID` tinyint NOT NULL,
  `sysName` tinyint NOT NULL,
  `regID` tinyint NOT NULL,
  `regName` tinyint NOT NULL,
  `availablePayouts` tinyint NOT NULL,
  `ptQualified` tinyint NOT NULL,
  `shipID` tinyint NOT NULL,
  `shipName` tinyint NOT NULL,
  `reservedBy` tinyint NOT NULL,
  `reservedDate` tinyint NOT NULL,
  `submittedBy` tinyint NOT NULL,
  `paid` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwkills`
--

DROP TABLE IF EXISTS `vwkills`;
/*!50001 DROP VIEW IF EXISTS `vwkills`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwkills` (
  `killID` tinyint NOT NULL,
  `killTime` tinyint NOT NULL,
  `crest_link` tinyint NOT NULL,
  `bcast` tinyint NOT NULL,
  `fit` tinyint NOT NULL,
  `victimName` tinyint NOT NULL,
  `victimID` tinyint NOT NULL,
  `corpID` tinyint NOT NULL,
  `corpName` tinyint NOT NULL,
  `sysID` tinyint NOT NULL,
  `sysName` tinyint NOT NULL,
  `regID` tinyint NOT NULL,
  `regName` tinyint NOT NULL,
  `availablePayouts` tinyint NOT NULL,
  `ptQualified` tinyint NOT NULL,
  `shipID` tinyint NOT NULL,
  `shipName` tinyint NOT NULL,
  `reservedBy` tinyint NOT NULL,
  `reservedDate` tinyint NOT NULL,
  `submittedBy` tinyint NOT NULL,
  `paid` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL,
  `note` tinyint NOT NULL,
  `deniedBy` tinyint NOT NULL,
  `deniedOn` tinyint NOT NULL,
  `payoutAmount` tinyint NOT NULL,
  `payoutType` tinyint NOT NULL,
  `paidOn` tinyint NOT NULL,
  `prefix` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwpaymentswreason`
--

DROP TABLE IF EXISTS `vwpaymentswreason`;
/*!50001 DROP VIEW IF EXISTS `vwpaymentswreason`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwpaymentswreason` (
  `killID` tinyint NOT NULL,
  `paidBy` tinyint NOT NULL,
  `reasonField` tinyint NOT NULL,
  `payoutAmount` tinyint NOT NULL,
  `timestamp` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwpayoutTypeByShip`
--

DROP TABLE IF EXISTS `vwpayoutTypeByShip`;
/*!50001 DROP VIEW IF EXISTS `vwpayoutTypeByShip`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwpayoutTypeByShip` (
  `shipName` tinyint NOT NULL,
  `typeName` tinyint NOT NULL,
  `amount` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwpayouts`
--

DROP TABLE IF EXISTS `vwpayouts`;
/*!50001 DROP VIEW IF EXISTS `vwpayouts`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwpayouts` (
  `typeName` tinyint NOT NULL,
  `id` tinyint NOT NULL,
  `payoutType` tinyint NOT NULL,
  `payoutAmount` tinyint NOT NULL,
  `typeID` tinyint NOT NULL,
  `payoutTypeID` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwptcap`
--

DROP TABLE IF EXISTS `vwptcap`;
/*!50001 DROP VIEW IF EXISTS `vwptcap`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwptcap` (
  `submittedBy` tinyint NOT NULL,
  `count` tinyint NOT NULL,
  `total` tinyint NOT NULL,
  `month` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwsysconreg`
--

DROP TABLE IF EXISTS `vwsysconreg`;
/*!50001 DROP VIEW IF EXISTS `vwsysconreg`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwsysconreg` (
  `sys_eve_id` tinyint NOT NULL,
  `sys_name` tinyint NOT NULL,
  `x` tinyint NOT NULL,
  `y` tinyint NOT NULL,
  `z` tinyint NOT NULL,
  `sec` tinyint NOT NULL,
  `con_name` tinyint NOT NULL,
  `reg_name` tinyint NOT NULL,
  `reg_id` tinyint NOT NULL,
  `con_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `walletJournal`
--

DROP TABLE IF EXISTS `walletJournal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `walletJournal` (
  `refid` bigint(20) NOT NULL,
  `corpid` bigint(20) NOT NULL,
  `wid` int(11) NOT NULL,
  `reftypeid` bigint(20) NOT NULL,
  `ownername1` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid1` bigint(20) NOT NULL,
  `ownername2` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `ownerid2` bigint(20) NOT NULL,
  `argname1` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `argid1` bigint(20) NOT NULL,
  `amount` float NOT NULL,
  `balance` float NOT NULL,
  `reason` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `tdate` datetime NOT NULL,
  `yyyy-mm` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `paid` int(11) DEFAULT '0',
  PRIMARY KEY (`refid`),
  KEY `ownerid2` (`ownerid2`),
  KEY `ownerid1` (`ownerid1`),
  KEY `reftypeid` (`reftypeid`),
  KEY `yyyy-mm` (`yyyy-mm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `vwdeniedPayments`
--

/*!50001 DROP TABLE IF EXISTS `vwdeniedPayments`*/;
/*!50001 DROP VIEW IF EXISTS `vwdeniedPayments`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwdeniedPayments` AS select `pd`.`reason` AS `reason`,`pd`.`timestamp` AS `deniedOn`,`k`.`killID` AS `killID`,`k`.`killTime` AS `killTime`,`k`.`crest_link` AS `crest_link`,`k`.`bcast` AS `bcast`,`k`.`fit` AS `fit`,`k`.`victimName` AS `victimName`,`k`.`victimID` AS `victimID`,`k`.`corpID` AS `corpID`,`k`.`corpName` AS `corpName`,`k`.`sysID` AS `sysID`,`k`.`sysName` AS `sysName`,`k`.`regID` AS `regID`,`k`.`regName` AS `regName`,`k`.`availablePayouts` AS `availablePayouts`,`k`.`ptQualified` AS `ptQualified`,`k`.`shipID` AS `shipID`,`k`.`shipName` AS `shipName`,`k`.`reservedBy` AS `reservedBy`,`k`.`reservedDate` AS `reservedDate`,`k`.`submittedBy` AS `submittedBy`,`k`.`paid` AS `paid`,`k`.`timestamp` AS `timestamp` from (`paymentsDenied` `pd` join `kills` `k` on((`k`.`killID` = `pd`.`killID`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwkills`
--

/*!50001 DROP TABLE IF EXISTS `vwkills`*/;
/*!50001 DROP VIEW IF EXISTS `vwkills`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwkills` AS select `k`.`killID` AS `killID`,`k`.`killTime` AS `killTime`,`k`.`crest_link` AS `crest_link`,`k`.`bcast` AS `bcast`,`k`.`fit` AS `fit`,`k`.`victimName` AS `victimName`,`k`.`victimID` AS `victimID`,`k`.`corpID` AS `corpID`,`k`.`corpName` AS `corpName`,`k`.`sysID` AS `sysID`,`k`.`sysName` AS `sysName`,`k`.`regID` AS `regID`,`k`.`regName` AS `regName`,`k`.`availablePayouts` AS `availablePayouts`,`k`.`ptQualified` AS `ptQualified`,`k`.`shipID` AS `shipID`,`k`.`shipName` AS `shipName`,`k`.`reservedBy` AS `reservedBy`,`k`.`reservedDate` AS `reservedDate`,`k`.`submittedBy` AS `submittedBy`,`k`.`paid` AS `paid`,`k`.`timestamp` AS `timestamp`,if((`k`.`paid` = 1),`pc`.`payoutNotes`,`pd`.`reason`) AS `note`,`pd`.`deniedBy` AS `deniedBy`,`pd`.`timestamp` AS `deniedOn`,`pc`.`payoutAmount` AS `payoutAmount`,`pt`.`typeName` AS `payoutType`,`pc`.`timestamp` AS `paidOn`,`pt`.`prefix` AS `prefix` from (((`kills` `k` left join `paymentsDenied` `pd` on((`pd`.`killID` = `k`.`killID`))) left join `paymentsCompleted` `pc` on((`pc`.`killID` = `k`.`killID`))) left join `payoutTypes` `pt` on((`pt`.`id` = `pc`.`payoutType`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwpaymentswreason`
--

/*!50001 DROP TABLE IF EXISTS `vwpaymentswreason`*/;
/*!50001 DROP VIEW IF EXISTS `vwpaymentswreason`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwpaymentswreason` AS select `pc`.`killID` AS `killID`,`pc`.`paidBy` AS `paidBy`,concat(`pt`.`prefix`,'-',`pc`.`killID`) AS `reasonField`,`pc`.`payoutAmount` AS `payoutAmount`,`pc`.`timestamp` AS `timestamp` from (`paymentsCompleted` `pc` join `payoutTypes` `pt` on((`pt`.`id` = `pc`.`payoutType`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwpayoutTypeByShip`
--

/*!50001 DROP TABLE IF EXISTS `vwpayoutTypeByShip`*/;
/*!50001 DROP VIEW IF EXISTS `vwpayoutTypeByShip`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwpayoutTypeByShip` AS select `p`.`typeName` AS `shipName`,`pt`.`typeName` AS `typeName`,max(if((`p`.`payoutType` = `pt`.`id`),`p`.`payoutAmount`,0)) AS `amount` from (`payouts` `p` join `payoutTypes` `pt`) where (`pt`.`active` = 1) group by `pt`.`typeName`,`p`.`typeName` order by `p`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwpayouts`
--

/*!50001 DROP TABLE IF EXISTS `vwpayouts`*/;
/*!50001 DROP VIEW IF EXISTS `vwpayouts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwpayouts` AS select `payouts`.`typeName` AS `typeName`,`payouts`.`id` AS `id`,`payoutTypes`.`typeName` AS `payoutType`,`payouts`.`payoutAmount` AS `payoutAmount`,`payouts`.`typeID` AS `typeID`,`payouts`.`payoutType` AS `payoutTypeID` from (`payouts` join `payoutTypes` on((`payoutTypes`.`id` = `payouts`.`payoutType`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwptcap`
--

/*!50001 DROP TABLE IF EXISTS `vwptcap`*/;
/*!50001 DROP VIEW IF EXISTS `vwptcap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwptcap` AS select `k`.`submittedBy` AS `submittedBy`,count(0) AS `count`,sum(`pc`.`payoutAmount`) AS `total`,date_format(`k`.`killTime`,'%Y-%m') AS `month` from (`kills` `k` left join `paymentsCompleted` `pc` on((`pc`.`killID` = `k`.`killID`))) where ((`k`.`paid` = 1) and (`pc`.`payoutType` = 4)) group by `k`.`submittedBy`,date_format(`k`.`killTime`,'%Y-%m') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwsysconreg`
--

/*!50001 DROP TABLE IF EXISTS `vwsysconreg`*/;
/*!50001 DROP VIEW IF EXISTS `vwsysconreg`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwsysconreg` AS select `mapSolarSystems`.`solarSystemID` AS `sys_eve_id`,`mapSolarSystems`.`solarSystemName` AS `sys_name`,(round(`mapSolarSystems`.`x`,0) / 10000000000000000) AS `x`,(round(`mapSolarSystems`.`y`,0) / 10000000000000000) AS `y`,(round(`mapSolarSystems`.`z`,0) / 10000000000000000) AS `z`,`mapSolarSystems`.`security` AS `sec`,`mapConstellations`.`constellationName` AS `con_name`,`mapRegions`.`regionName` AS `reg_name`,`mapRegions`.`regionID` AS `reg_id`,`mapConstellations`.`constellationID` AS `con_id` from ((`mapSolarSystems` join `mapConstellations` on((`mapConstellations`.`constellationID` = `mapSolarSystems`.`constellationID`))) join `mapRegions` on((`mapRegions`.`regionID` = `mapConstellations`.`regionID`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-24  2:26:32
