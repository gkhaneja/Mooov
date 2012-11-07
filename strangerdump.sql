-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: stranger
-- ------------------------------------------------------
-- Server version	5.5.24-0ubuntu0.12.04.1

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
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `src_lattitude` double(11,8) DEFAULT '0.00000000',
  `src_longitude` double(11,8) DEFAULT '0.00000000',
  `dst_lattitude` double(11,8) DEFAULT '0.00000000',
  `dst_longitude` double(11,8) DEFAULT '0.00000000',
  `created` datetime DEFAULT NULL,
  `lastupd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`),
  KEY `src_lattitude` (`src_lattitude`,`src_longitude`,`dst_lattitude`,`dst_longitude`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request`
--

LOCK TABLES `request` WRITE;
/*!40000 ALTER TABLE `request` DISABLE KEYS */;
INSERT INTO `request` VALUES (1,1,55.55000000,55.55000000,55.55000000,55.55000000,NULL,'2012-09-15 09:03:00'),(2,2,55.00000000,55.00000000,56.00000000,56.00000000,NULL,'2012-10-07 10:19:24'),(3,3,54.60000000,54.60000000,54.60000000,54.60000000,NULL,'2012-09-15 09:06:05'),(4,4,54.00000000,54.00000000,54.00000000,54.00000000,NULL,'2012-09-15 09:06:05'),(5,9,55.00000000,55.00000000,56.00000000,56.00000000,NULL,'2012-10-06 15:20:12'),(7,10,55.00000000,55.00000000,56.00000000,56.00000000,NULL,'2012-10-06 15:20:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'gourav',NULL,'2012-09-15 10:07:50','gourav','khaneja',NULL),(2,'rahul',NULL,'2012-09-15 10:07:50','rahul','saxena',NULL),(3,'arpit',NULL,'2012-09-15 10:07:50','arpit','mishra',NULL),(4,'mayank',NULL,'2012-09-15 10:07:50','mayank','jaiswal',NULL),(9,'bourne',NULL,'2012-09-30 13:27:29','jason','bourne',NULL),(11,'bourn',NULL,'2012-09-30 17:08:46','jason','bourne',NULL),(12,'',NULL,'2012-10-06 09:24:45',NULL,NULL,'550e8400-e29b-41d4-a716-446655440000'),(14,'',NULL,'2012-10-06 10:09:36',NULL,NULL,'550e8400-e29b-41d4-a716-446655440001'),(15,NULL,NULL,'2012-10-06 10:11:33',NULL,NULL,'550e8400-e29b-41d4-a716-446655440002'),(16,NULL,NULL,'2012-10-06 10:28:03',NULL,NULL,'550e8400-e29b-41d4-a716-446655440003'),(17,NULL,NULL,'2012-10-06 10:31:53',NULL,NULL,'f6412286-3439-41df-8208-cd469721fc01'),(18,NULL,NULL,'2012-10-06 11:25:07',NULL,NULL,'550e8400-e29b-41d4-a716-446655440004'),(19,NULL,NULL,'2012-10-06 11:25:35',NULL,NULL,'550e8400-e29b-41d4-a716-446655440004'),(20,NULL,NULL,'2012-10-06 11:47:43',NULL,NULL,'550e8400-e29b-41d4-a716-446655440004'),(21,NULL,NULL,'2012-10-06 13:55:29',NULL,NULL,'f6412286-3439-41df-8208-cd469721fc01'),(22,NULL,NULL,'2012-10-06 13:56:53',NULL,NULL,'f6412286-3439-41df-8208-cd469721fc01'),(23,NULL,NULL,'2012-10-06 13:58:14',NULL,NULL,'f6412286-3439-41df-8208-cd469721fc01'),(24,NULL,NULL,'2012-10-06 14:14:16',NULL,NULL,'550e8400-e29b-41d4-a716-446655440005'),(25,NULL,NULL,'2012-10-06 14:15:35',NULL,NULL,'550e8400-e29b-41d4-a716-446655440005'),(26,NULL,NULL,'2012-10-06 14:16:06',NULL,NULL,'550e8400-e29b-41d4-a716-446655440006'),(27,NULL,NULL,'2012-10-07 14:08:16',NULL,NULL,'550e8400-e29b-41d4-a716-446655440007');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-07 22:46:49
