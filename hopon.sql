-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hopon
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.04.2

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
-- Table structure for table `exceptions`
--

DROP TABLE IF EXISTS `exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exceptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `template` varchar(500) DEFAULT NULL,
  `solution_template` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exceptions`
--

LOCK TABLES `exceptions` WRITE;
/*!40000 ALTER TABLE `exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `exceptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mumbai_dst`
--

DROP TABLE IF EXISTS `mumbai_dst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mumbai_dst` (
  `row_id` int(10) unsigned NOT NULL,
  `col_id` int(10) unsigned NOT NULL,
  `users` varchar(500) DEFAULT NULL,
  KEY `row_id` (`row_id`,`col_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mumbai_dst`
--

LOCK TABLES `mumbai_dst` WRITE;
/*!40000 ALTER TABLE `mumbai_dst` DISABLE KEYS */;
INSERT INTO `mumbai_dst` VALUES (106,89,'1,2,3,37,10'),(106,88,'1,2,3,37,10'),(105,89,'1,2,3,37,10'),(105,88,'1,2,3,37,10'),(218,85,'38,39,40'),(218,84,'38,39,40'),(217,85,',40'),(217,84,',40'),(219,85,'37,38,39,40'),(219,84,'37,38,39,40'),(219,86,'40'),(218,86,'40'),(214,38,'39'),(214,37,'39'),(213,38,'39'),(213,37,'39');
/*!40000 ALTER TABLE `mumbai_dst` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mumbai_src`
--

DROP TABLE IF EXISTS `mumbai_src`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mumbai_src` (
  `row_id` int(10) unsigned NOT NULL,
  `col_id` int(10) unsigned NOT NULL,
  `users` varchar(500) DEFAULT NULL,
  KEY `row_id` (`row_id`,`col_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mumbai_src`
--

LOCK TABLES `mumbai_src` WRITE;
/*!40000 ALTER TABLE `mumbai_src` DISABLE KEYS */;
INSERT INTO `mumbai_src` VALUES (101,84,'1,2,37,10'),(101,83,'1,2,37,10'),(100,84,'1'),(100,83,'1'),(102,84,'2,37,10'),(102,83,'2,37,10'),(104,84,'3'),(104,83,'3'),(103,84,'3'),(103,83,'3'),(218,85,'38,39,40'),(218,84,'38,39,40'),(217,85,',40'),(217,84,',40'),(219,85,'37,38,39,40'),(219,84,'37,38,39,40'),(219,86,'40'),(218,86,'40'),(214,38,'39'),(214,37,'39'),(213,38,'39'),(213,37,'39');
/*!40000 ALTER TABLE `mumbai_src` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `src_latitude` double(11,8) DEFAULT NULL,
  `src_longitude` double(11,8) DEFAULT '0.00000000',
  `dst_latitude` double(11,8) DEFAULT NULL,
  `dst_longitude` double(11,8) DEFAULT '0.00000000',
  `created` datetime DEFAULT NULL,
  `lastupd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `src_locality` varchar(255) DEFAULT NULL,
  `src_address` varchar(255) DEFAULT NULL,
  `dst_locality` varchar(255) DEFAULT NULL,
  `dst_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`),
  KEY `src_lattitude` (`src_latitude`,`src_longitude`,`dst_latitude`,`dst_longitude`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request`
--

LOCK TABLES `request` WRITE;
/*!40000 ALTER TABLE `request` DISABLE KEYS */;
INSERT INTO `request` VALUES (1,1,19.00000000,72.90000000,19.00500000,72.90500000,NULL,'2012-11-28 20:02:32',NULL,NULL,NULL,NULL),(2,2,19.00100000,72.90000000,19.00500000,72.90500000,NULL,'2012-11-28 20:03:18',NULL,NULL,NULL,NULL),(3,3,19.00300000,72.90000000,19.00500000,72.90500000,NULL,'2012-11-28 20:03:39',NULL,NULL,NULL,NULL),(4,4,19.12570000,72.91026000,19.13450000,72.91050000,NULL,'2012-11-27 17:47:16',NULL,NULL,NULL,NULL),(5,9,19.12510000,72.90320000,19.12560000,72.90320000,NULL,'2012-11-27 16:38:39',NULL,NULL,NULL,NULL),(7,10,19.00120000,72.90000000,19.00500000,72.90500000,NULL,'2012-11-28 20:15:57',NULL,NULL,NULL,NULL),(8,34,19.11798600,72.90163700,19.11798600,72.90163700,NULL,'2012-11-27 20:12:39',NULL,NULL,NULL,NULL),(9,35,19.11802500,72.90162900,19.11802500,72.90162900,NULL,'2012-11-27 20:30:38',NULL,NULL,NULL,NULL),(10,36,19.11827900,72.90166700,19.11827900,72.90166700,NULL,'2012-11-27 21:55:40',NULL,NULL,NULL,NULL),(12,38,19.11828400,72.90159600,19.11828400,72.90159600,NULL,'2012-11-29 05:49:07',NULL,NULL,NULL,NULL),(13,39,19.11309700,72.85465600,19.11309700,72.85465600,NULL,'2012-11-30 07:04:47',NULL,NULL,NULL,NULL),(14,40,19.11760100,72.90149600,19.11760100,72.90149600,NULL,'2012-12-01 17:56:03',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `response`
--

DROP TABLE IF EXISTS `response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `response` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `lastupd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `response`
--

LOCK TABLES `response` WRITE;
/*!40000 ALTER TABLE `response` DISABLE KEYS */;
/*!40000 ALTER TABLE `response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `lastupd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username_2` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'gourav',NULL,'2012-09-15 10:07:50','gourav','khaneja',NULL),(2,'rahul',NULL,'2012-09-15 10:07:50','rahul','saxena',NULL),(3,'arpit',NULL,'2012-09-15 10:07:50','arpit','mishra',NULL),(4,'mayank',NULL,'2012-09-15 10:07:50','mayank','jaiswal',NULL),(9,'bourne',NULL,'2012-09-30 13:27:29','jason','bourne',NULL),(10,'bourn',NULL,'2012-11-27 21:07:24','jason','bourne',NULL),(28,NULL,NULL,'2012-11-24 17:08:38','rahul','saxena','arpit'),(29,NULL,NULL,'2012-11-20 22:39:46',NULL,NULL,'dc30e44e-b9e6-4c18-a3cf-d7b5621166a1'),(30,NULL,NULL,'2012-11-24 14:52:51',NULL,NULL,'xyz'),(31,NULL,NULL,'2012-11-27 17:10:17',NULL,NULL,'75720c6e-d9b0-448c-8f37-ce3dd9feb1ed'),(32,NULL,NULL,'2012-11-27 19:25:57',NULL,NULL,'93a753ed-b800-4088-b3ef-93d045807547'),(33,NULL,NULL,'2012-11-27 19:30:14',NULL,NULL,'d230f3c3-3df6-41c5-bf57-bd7a1f1ed51f'),(34,NULL,NULL,'2012-11-27 19:34:16',NULL,NULL,'c1dbf504-824f-4222-b4d8-9bf32982c53d'),(35,NULL,NULL,'2012-11-27 20:16:19',NULL,NULL,'c8d5d3a2-8276-4037-926b-a7d96fa2cedb'),(36,NULL,NULL,'2012-11-27 20:32:25',NULL,NULL,'ee9aa889-fc98-49bd-a0a0-163ae15b4ffa'),(37,NULL,NULL,'2012-11-27 21:57:29',NULL,NULL,'ee1918f5-708b-46cd-9e63-e7625f65db69'),(38,NULL,NULL,'2012-11-29 05:48:40',NULL,NULL,'b3dd22fc-5c7f-4fb6-b76c-1410ae644fbf'),(39,NULL,NULL,'2012-11-29 05:52:35',NULL,NULL,'c3873fa6-a5ac-48c6-9270-9caef2d774e9'),(40,NULL,NULL,'2012-11-29 18:40:39',NULL,NULL,'e53a5db7-5493-4163-95f0-5e84a6abb8d4');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_details` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(200) unsigned DEFAULT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `fbid` varchar(200) DEFAULT NULL,
  `fbtoken` varchar(255) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `workplace` longblob,
  `hometown` longblob,
  `education` longblob,
  `location` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_details`
--

LOCK TABLES `user_details` WRITE;
/*!40000 ALTER TABLE `user_details` DISABLE KEYS */;
INSERT INTO `user_details` VALUES (1,10,'Rahul','Saxena','saxena.rahul.kgp','536309667','AAACEdEose0cBACZBjCDvyUpvaj3RVCh2SQRXr7Cb0ZAd3ZCkVhL3boccSUXxL9ZBryjr0dgBGBuuBpiabftzZClZAGcIVGpZAJgZCsE5yuJGpwZDZD','rahul1iitkgp@gmail.com',NULL,'male','a:3:{i:0;a:4:{s:8:\"employer\";a:2:{s:2:\"id\";s:15:\"146117198744399\";s:4:\"name\";s:5:\"Saavn\";}s:8:\"location\";a:2:{s:2:\"id\";s:15:\"114759761873412\";s:4:\"name\";s:26:\"Mumbai, Maharashtra, India\";}s:8:\"position\";a:2:{s:2:\"id\";s:15:\"109542932398298\";s:4:\"name\";s:17:\"Software Engineer\";}s:10:\"start_date\";s:7:\"0000-00\";}i:1;a:6:{s:8:\"employer\";a:2:{s:2:\"id\";s:15:\"116917964994152\";s:4:\"name\";s:6:\"Nomura\";}s:8:\"location\";a:2:{s:2:\"id\";s:15:\"114759761873412\";s:4:\"name\";s:26:\"Mumbai, Maharashtra, India\";}s:8:\"position\";a:2:{s:2:\"id\";s:15:\"144785808873611\";s:4:\"name\";s:7:\"Analyst\";}s:11:\"description\";s:23:\"Fixed Income Technology\";s:10:\"start_date\";s:7:\"2010-07\";s:8:\"end_date\";s:7:\"2012-08\";}i:2;a:1:{s:8:\"employer\";a:2:{s:2:\"id\";s:15:\"101158036596337\";s:4:\"name\";s:37:\"Nomura Services India Private Limited\";}}}','a:2:{s:2:\"id\";s:15:\"107675019262016\";s:4:\"name\";s:15:\"Kota, Rajasthan\";}','a:5:{i:0;a:2:{s:6:\"school\";a:2:{s:2:\"id\";s:15:\"108067102560681\";s:4:\"name\";s:13:\"st pauls kota\";}s:4:\"type\";s:11:\"High School\";}i:1;a:3:{s:6:\"school\";a:2:{s:2:\"id\";s:15:\"114368475246617\";s:4:\"name\";s:28:\"Saint Paul\'s Sr. Sec. School\";}s:4:\"type\";s:11:\"High School\";s:4:\"with\";a:1:{i:0;a:2:{s:2:\"id\";s:9:\"563883803\";s:4:\"name\";s:12:\"Animesh Jain\";}}}i:2;a:2:{s:6:\"school\";a:2:{s:2:\"id\";s:15:\"271667269604161\";s:4:\"name\";s:13:\"IIT Kharagpur\";}s:4:\"type\";s:7:\"College\";}i:3;a:3:{s:6:\"school\";a:2:{s:2:\"id\";s:15:\"111854795497606\";s:4:\"name\";s:33:\"University of Southern California\";}s:4:\"type\";s:7:\"College\";s:4:\"with\";a:1:{i:0;a:2:{s:2:\"id\";s:10:\"1151517930\";s:4:\"name\";s:12:\"Pankaj Rajak\";}}}i:4;a:3:{s:6:\"school\";a:2:{s:2:\"id\";s:15:\"271667269604161\";s:4:\"name\";s:13:\"IIT Kharagpur\";}s:13:\"concentration\";a:1:{i:0;a:2:{s:2:\"id\";s:15:\"104076956295773\";s:4:\"name\";s:16:\"Computer Science\";}}s:4:\"type\";s:15:\"Graduate School\";}}','a:2:{s:2:\"id\";s:15:\"114759761873412\";s:4:\"name\";s:26:\"Mumbai, Maharashtra, India\";}'),(2,40,'Arpit','Mishra','arpitmishra87','649462845','BAAG62CHo70MBACIS8D4LsTLdKtI3TM9GZCvP0kHHZCjD9385jTggOolRZA2f7aWozo13dD0JEGvW4PVedlcLghbSse7ZASkGcUhHk4CUhPFHFTwRnQR3ZAT4DUv7Ghm4ZD','',NULL,'male','a:2:{i:0;a:4:{s:8:\"employer\";a:2:{s:2:\"id\";s:15:\"111113182241968\";s:4:\"name\";s:6:\"Nomura\";}s:8:\"location\";a:2:{s:2:\"id\";s:15:\"114759761873412\";s:4:\"name\";s:26:\"Mumbai, Maharashtra, India\";}s:8:\"position\";a:2:{s:2:\"id\";s:15:\"144785808873611\";s:4:\"name\";s:7:\"Analyst\";}s:10:\"start_date\";s:7:\"2010-07\";}i:1;a:5:{s:8:\"employer\";a:2:{s:2:\"id\";s:15:\"191746720878870\";s:4:\"name\";s:20:\"Microsoft, Hyderabad\";}s:8:\"location\";a:2:{s:2:\"id\";s:15:\"115200305158163\";s:4:\"name\";s:25:\"Hyderabad, Andhra Pradesh\";}s:8:\"position\";a:2:{s:2:\"id\";s:15:\"108703555821060\";s:4:\"name\";s:6:\"Intern\";}s:10:\"start_date\";s:7:\"2009-05\";s:8:\"end_date\";s:7:\"2009-07\";}}','a:2:{s:2:\"id\";s:15:\"112863308729303\";s:4:\"name\";s:6:\"Raipur\";}',NULL,'a:2:{s:2:\"id\";s:15:\"114759761873412\";s:4:\"name\";s:26:\"Mumbai, Maharashtra, India\";}');
/*!40000 ALTER TABLE `user_details` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-02 10:55:31
