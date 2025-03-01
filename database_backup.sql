-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: multimedia
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `iframe_url` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,'Basketball','Basketball game ','https://www.onlinegames.io/basketball-king/','2025-01-04 00:27:02');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('document','music','video') NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (1,'music','Song 1','http://localhost/media/music/song1.mp3'),(2,'video','Video 1','http://localhost/media/videos/video1.mp4'),(3,'document','Document 1','http://localhost/media/documents/doc1.pdf');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `music`
--

DROP TABLE IF EXISTS `music`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `music`
--

LOCK TABLES `music` WRITE;
/*!40000 ALTER TABLE `music` DISABLE KEYS */;
INSERT INTO `music` VALUES (2,'Kuba','Kubas','mus_67786151d67ee1.91574170.mp3','2025-01-03 23:14:41');
/*!40000 ALTER TABLE `music` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `born_date` date DEFAULT NULL,
  `user_role` enum('Admin','User','Editor','Provider') NOT NULL DEFAULT 'User',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo_id` varchar(255) DEFAULT NULL,
  `video_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'John Doe','john.doe@example.com','password123','2024-11-11 15:15:07','123 Main St','New York','1985-03-25','Admin','2024-11-11 15:15:07',NULL,NULL),(4,'Jane Smith','jane.smith@example.com','password456','2024-11-11 15:15:07','456 Oak St','Los Angeles','1990-06-15','User','2024-11-11 15:15:07',NULL,NULL),(5,'Michael Johnson','michael.johnson@example.com','password789','2024-11-11 15:15:07','789 Pine St','Chicago','1987-09-09','Editor','2024-11-11 15:15:07',NULL,NULL),(6,'Lisa Williams','lisa.williams@example.com','password101','2024-11-11 15:15:07','321 Elm St','Houston','1983-12-20','Provider','2024-11-11 15:15:07',NULL,NULL),(7,'David Brown','david.brown@example.com','password111','2024-11-11 15:15:07','654 Cedar St','Phoenix','1992-01-10','User','2024-11-11 15:15:07',NULL,NULL),(8,'Emily Davis','emily.davis@example.com','password222','2024-11-11 15:15:07','987 Willow St','Philadelphia','1989-07-18','Admin','2024-11-11 15:15:07',NULL,NULL),(9,'Chris Wilson','chris.wilson@example.com','password333','2024-11-11 15:15:07','159 Maple St','San Antonio','1994-11-22','Provider','2024-11-11 15:15:07',NULL,NULL),(10,'Sophia Moore','sophia.moore@example.com','password444','2024-11-11 15:15:07','753 Birch St','San Diego','1991-03-02','Editor','2024-11-11 15:15:07',NULL,NULL),(11,'James Taylor','james.taylor@example.com','password555','2024-11-11 15:15:07','369 Redwood St','Dallas','1988-05-30','User','2024-11-11 15:15:07',NULL,NULL),(12,'Olivia Anderson','olivia.anderson@example.com','password666','2024-11-11 15:15:07','852 Spruce St','San Jose','1995-08-12','Admin','2024-11-11 15:15:07',NULL,NULL),(13,'Daniel Thomas','daniel.thomas@example.com','password777','2024-11-11 15:15:07','147 Aspen St','Austin','1986-10-14','Editor','2024-11-11 15:15:07',NULL,NULL),(14,'Mia Jackson','mia.jackson@example.com','password888','2024-11-11 15:15:07','963 Sycamore St','Jacksonville','1993-09-28','Provider','2024-11-11 15:15:07',NULL,NULL),(15,'Ethan White','ethan.white@example.com','password999','2024-11-11 15:15:07','369 Fir St','Columbus','1984-04-25','User','2024-11-11 15:15:07',NULL,NULL),(16,'Isabella Harris','isabella.harris@example.com','password1010','2024-11-11 15:15:07','456 Palm St','Fort Worth','1990-11-07','Editor','2024-11-11 15:15:07',NULL,NULL),(17,'Matthew Martin','matthew.martin@example.com','password1111','2024-11-11 15:15:07','159 Poplar St','Charlotte','1992-02-15','Admin','2024-11-11 15:15:07',NULL,NULL),(18,'Ava Martinez','ava.martinez@example.com','$2y$10$kn3aXWoFWzQIA6LrPoEvAeNvDE7ZbDEF5i2sDSGz/8S8W8y9FmMZy','2024-11-11 15:15:07','321 Oakwood St','Indianapolis','1985-06-10','User','2024-11-21 18:29:46',NULL,NULL),(19,'Alexander Clark','alexander.clark@example.com','$2y$10$vYIBz5hAk8ExBtvkcJ9oLeHEQB6jhG7vnnf1xueYXl8h3jkwHtjQy','2024-11-11 15:15:07','753 Cedarwood St','San Francisco','1989-08-05','Provider','2024-11-29 16:56:11',NULL,NULL),(20,'Sophia Rodriguez','sophia.rodriguez@example.com','password1414','2024-11-11 15:15:07','987 Aspenwood St','Seattle','1987-10-27','Admin','2024-11-11 15:15:07',NULL,NULL),(21,'William Lee','william.lee@example.com','password1515','2024-11-11 15:15:07','654 Maplewood St','Denver','1991-07-14','User','2024-11-11 15:15:07',NULL,NULL),(22,'Isabella Walker','isabella.walker@example.com','password1616','2024-11-11 15:15:07','123 Redwoodwood St','Washington','1994-03-11','Provider','2024-11-11 15:15:07',NULL,NULL),(23,'Logan Scott','logan.scott@example.com','password1717','2024-11-11 15:15:07','456 Birchwood St','Boston','1988-12-01','Editor','2024-11-11 15:15:07',NULL,NULL),(24,'Charlotte Green','charlotte.green@example.com','$2y$10$Z2jPSqlAO9msYlcY8/kWAux1nxfi8GNGN5.OFEL.gM2ZF5XJVpJiy','2024-11-11 15:15:07','369 Sprucewood St','El Paso','1993-09-19','User','2024-11-21 20:01:06',NULL,NULL),(25,'Noah Hall','noah.hall@example.com','password1919','2024-11-11 15:15:07','789 Pinewood St','Detroit','1985-05-07','Admin','2024-11-11 15:15:07',NULL,NULL),(26,'Amelia Wright','amelia.wright@example.com','$2y$10$EdlvgVUFIm21y4YLAmZ2VumhEsvgeO6Fk96SFzASkkdnMBbVyvi6a','2024-11-11 15:15:07','963 Firwood St','Nashville','1990-07-21','Editor','2024-11-21 19:49:35',NULL,NULL),(27,'Lucas Hill','lucas.hill@example.com','password2121','2024-11-11 15:15:07','321 Willowwood St','Memphis','1991-04-18','Provider','2024-11-11 15:15:07',NULL,NULL),(28,'Tomas','tomas@wp.pl','$2y$10$I2NQ6VR61w7IZB5nfzotn.yeR./A278NlQtBQ0h2JTRAd6bEaI6M2','2024-11-11 15:18:03','Pszczynska 112','Gliwice','1999-04-15','Editor','2024-11-11 15:18:03',NULL,NULL),(29,'Matteus','matteus@gmail.com','$2y$10$UincFI5LKLrKd3zqVzAh3.lsFUShbq2mVABKx4UTcETkTs5k0JWFO','2024-11-11 15:29:55','Lea','Zabrze','2005-05-04','Admin','2024-11-11 15:29:55',NULL,NULL),(30,'Pawel','pawel@wp.pl','$2y$10$w/QhnU9gb/kCu6GTITeUxeUJHyfDtDodIb39WI2p.Wx4c1yn9KkxK','2024-11-11 15:42:45','Elsnera','Poznan','2000-03-03','User','2024-11-11 15:42:45',NULL,NULL),(37,'Mat','mat@wp.pl','$2y$10$AU.qKfXt41uGb0s4ehKeXu2igGeT3YYtyv0Rfh4Sl.cDcZ1Ub2X/W','2024-11-14 15:23:39','Lea210','Paris','2000-05-31','Admin','2024-11-14 15:23:39',NULL,NULL),(41,'jan','jan@wp.pl','$2y$10$/G6JamPaLUzHdHYIeQppMuef45szeG6OD.LvostujlBqqFJEDYMga','2024-11-14 17:19:27','jankowa','jankowo','2001-05-15','User','2024-11-14 17:19:27',NULL,NULL),(42,'maciej','maciej1@wp.pl','$2y$10$mePC3GJN0S5py9OC9n6FLuQb.FSfMgXthts7eVhN/Rl1VZgAZC6Le','2024-11-21 13:06:21','lea','poznan','2011-11-10','User','2024-11-21 13:06:21',NULL,NULL),(43,'kubas','kubas12@wp.pl','$2y$10$qm8fmfxpiVILYhWnMiUveexW8ujoNeva4Sz.s9UzncLidB.jFDOFq','2024-11-21 17:39:29','kubasowo','kubasow','2001-11-10','User','2024-11-21 17:39:29',NULL,NULL),(47,'aaa','aaa@aa.pl','$2y$10$ouemiZSPVyZ6b5Ez7DkzjeCCRmZL9g5bq.LbgPwnycJBDdO9.dn5G','2024-12-05 15:46:54','aaaaa','aaawa','2002-02-01','Editor','2024-12-05 15:46:54',NULL,NULL),(48,'Kub','kub12@wp.pl','$2y$10$WbeBZMyJ9fd2r.yRZDnx.OyVec37F/r90EfNLh0.POZaxKs6Py3WC','2024-12-05 17:04:05','kubaso','kubasow','1999-11-10','User','2024-12-05 17:04:05',NULL,NULL),(49,'kubasoa','kubasoa@wp.pl','$2y$10$Nn0.KH9CKXoCE/7LKMCRnexbS6Yex2WcBkCH4/bOjOJi.bwTHwz5S','2024-12-05 17:05:02','kubasooo','kubwq','2002-10-09','User','2024-12-05 17:05:02',NULL,NULL),(50,'Kuba','kuba@gmail.com','$2y$10$B7/eRh/YUTs4sgfa5tUqe..fWACfpuPfm7xzF7evYNM6AfvKqrm0q','2024-12-05 17:08:57','kubaso','kubse','2000-12-31','User','2024-12-05 17:08:57',NULL,NULL),(51,'kuba','kubas@wp.pl','$2y$10$9bdpJ/.29KlnzLaK0l0a4OA2Vi3Hg2Mep8pCSlbGbjEcqyLfopvTq','2024-12-05 17:51:12','kubasowa','kubus','2001-01-01','User','2024-12-05 17:51:12',NULL,NULL),(52,'Kuba','kuba121@wp.pl','$2y$10$QyiEmaAKa2FpVsH0At8dFeWsOfTwnoqfQhQ3qiGB.spbNjR8fk6Tu','2025-01-02 13:59:52','asdasd','asrqwr','2001-11-10','User','2025-01-02 13:59:52',NULL,NULL),(53,'Wojtek','Koras@wp.pl','$2y$10$xs2BwuramKMrrE/ZMZlLKOrDkcHEUdtqYi.K4q1YQnp1VDBzs22Ba','2025-01-02 14:27:31','korasow','korasowo','2002-12-11','User','2025-01-02 14:27:31',NULL,NULL),(54,'Pawel ','pawel09@gmail.com','$2y$10$Ga1u6r2BcY2bbI1Su5rQ1exYNoR9I20SGDcGZjJVqLjWQWloDyYr6','2025-01-02 14:31:43','pawelowo','pawelow','2000-12-31','User','2025-01-02 14:31:43','img_6776a34f651690.17916770.jpg',NULL),(55,'Kuba','kubam2001@wp.pl','$2y$10$WUe0n8nv6h2dZoKa1VToH.2fInEQIWYTXIB29nt6vtslNXjB2qdP6','2025-01-03 18:10:36','Kozielska','Gliwice','2001-04-16','Admin','2025-01-03 18:10:36','img_6778281c2e2280.87757848.jpg','vid_6778281c303062.02217219.mp4'),(56,'Tom','tom@wp.pl','$2y$10$.gGOrp1NxI3kmnK83q/q..ZZVcIUVBPFWqwTGGTcWPtgFiGoxYYni','2025-01-03 23:18:39','tomowo','tomo','2003-09-08','User','2025-01-03 23:18:39',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
INSERT INTO `videos` VALUES (1,'Waka','Shakira','https://www.youtube.com/watch?v=pRpeEdMmmQ0&ab_channel=shakiraVEVO','2025-01-03 22:34:39');
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-16 16:31:43
