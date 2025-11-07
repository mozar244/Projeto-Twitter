-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: usuario
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `privilege_level` enum('basic','intermediate','advanced','moderator','admin','banned','suspended') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'basic',
  `points` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Alex','alex@adm.com','$2y$12$zbogwXykw3Rj6maCFJIbbe7i2yMJC/TtHv5qvpKK2UuP5NE4qfwTu',NULL,'2025-11-03 17:42:41','admin',30),(4,'Igor','igor@gmail.com','$2y$12$ZKa7dYnGdXvxbTf3rZCKWOVqOTDfwxb1FfYmjA9WaK/50kFrp.bJG',NULL,'2025-11-04 19:19:06','moderator',0),(5,'Maria','maria@goias.gov.br','$2y$12$bujVAJTWET4MuRtE0MIu3eGs7GSXoflrATYBU3ham.b9eb8RMzNka',NULL,'2025-11-04 19:22:16','basic',0),(6,'Mozar','mozarguimaraesjr@gmail.com','$2y$12$jiRj19rxFNeFxg9GFrCvHuoYGzGrpytfpAo1HfNWMWdhaN.hLD8HS',NULL,'2025-11-06 21:34:50','basic',0),(9,'Kratz','kratz@email.com','$2y$12$Xxv8avOL7bXa4GlCPx5WtOpNh1RwF84NpxCYOVGtrkS4KWpzvCvdW',NULL,'2025-11-07 01:01:05','admin',0),(10,'Nickolas','nickolas@gmail.com','$2y$12$Kvm/8/7XsYD6WYBS01n7TejokglkrhrLjPDhSzPB/2lODw9KR5hAS',NULL,'2025-11-07 10:02:15','basic',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07  7:57:43
