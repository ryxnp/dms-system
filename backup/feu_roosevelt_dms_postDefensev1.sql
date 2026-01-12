-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: feu_roosevelt_dms
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
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_trail` (
  `audit_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit` (`user_id`,`timestamp`),
  CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
INSERT INTO `audit_trail` VALUES (1,3,'User Login','user',3,'::1','2025-11-15 09:59:16'),(2,4,'User Login','user',4,'::1','2025-11-15 10:01:33'),(3,3,'User Login','user',3,'::1','2025-11-15 10:07:21'),(4,1,'User Login','user',1,'::1','2025-11-15 10:08:38'),(5,1,'User Login','user',1,'::1','2025-11-15 14:29:21'),(6,1,'User Login','user',1,'::1','2025-11-15 23:28:38'),(7,1,'User Login','user',1,'::1','2025-11-15 23:33:37'),(8,1,'User Login','user',1,'::1','2025-11-15 23:48:44'),(9,3,'User Login','user',3,'::1','2025-11-16 07:28:36'),(10,1,'User Login','user',1,'::1','2025-11-16 07:30:20'),(11,3,'User Login','user',3,'::1','2025-11-16 07:31:52'),(12,3,'User Login','user',3,'::1','2025-11-16 07:36:32'),(13,5,'User Login','user',5,'::1','2025-11-16 07:54:22'),(14,1,'User Login','user',1,'::1','2025-11-16 08:35:06'),(15,3,'User Login','user',3,'::1','2025-11-16 10:03:04'),(16,5,'User Login','user',5,'::1','2025-11-16 11:40:52'),(17,3,'User Login','user',3,'::1','2025-11-16 11:51:20'),(18,2,'User Login','user',2,'::1','2025-11-16 13:58:43'),(19,3,'User Login','user',3,'::1','2025-11-16 14:19:56'),(20,1,'User Login','user',1,'::1','2025-11-16 17:06:22'),(21,6,'User Login','user',6,'::1','2025-11-16 20:22:18'),(22,6,'User Login','user',6,'::1','2025-11-16 20:22:25'),(23,6,'User Login','user',6,'::1','2025-11-16 20:23:09'),(24,6,'User Login','user',6,'::1','2025-11-16 20:23:14'),(25,6,'User Login','user',6,'::1','2025-11-16 20:23:21'),(26,6,'User Login','user',6,'::1','2025-11-16 20:23:26'),(27,6,'User Login','user',6,'::1','2025-11-16 20:24:20'),(28,6,'User Login','user',6,'::1','2025-11-16 20:25:49'),(29,6,'User Login','user',6,'::1','2025-11-16 20:25:53'),(30,6,'User Login','user',6,'::1','2025-11-16 20:30:41'),(31,6,'User Login','user',6,'::1','2025-11-16 20:30:51'),(32,6,'User Login','user',6,'::1','2025-11-16 20:40:29'),(33,6,'User Login','user',6,'::1','2025-11-16 20:40:33'),(34,3,'User Login','user',3,'::1','2025-11-16 20:41:01'),(35,6,'User Login','user',6,'::1','2025-11-16 22:38:59'),(36,1,'User Login','user',1,'::1','2025-12-12 09:30:11'),(37,6,'User Login','user',6,'::1','2025-12-12 14:17:29'),(38,3,'User Login','user',3,'::1','2025-12-12 14:18:52'),(39,1,'User Login','user',1,'::1','2025-12-12 14:21:00'),(40,3,'User Login','user',3,'::1','2025-12-12 14:25:05'),(41,2,'User Login','user',2,'::1','2025-12-12 14:26:27'),(42,1,'User Login','user',1,'::1','2025-12-12 14:30:44'),(43,1,'Updated Scholarship Status','scholarship_application',4,NULL,'2025-12-12 14:48:12'),(44,1,'User Login','user',1,'::1','2025-12-12 16:24:20'),(45,2,'User Login','user',2,'::1','2025-12-12 16:24:50'),(46,3,'User Login','user',3,'::1','2025-12-12 16:25:04'),(47,6,'User Login','user',6,'::1','2025-12-12 16:27:49'),(48,2,'User Login','user',2,'::1','2025-12-12 16:34:25'),(49,1,'User Login','user',1,'::1','2025-12-12 16:36:54'),(50,1,'Added to Dean\'s List','dean_list',0,NULL,'2025-12-12 17:00:30'),(51,1,'User Login','user',1,'27.49.10.164','2025-12-12 17:22:41'),(52,2,'User Login','user',2,'27.49.10.164','2025-12-12 17:22:55'),(53,3,'User Login','user',3,'27.49.10.164','2025-12-12 17:23:37'),(54,6,'User Login','user',6,'27.49.10.164','2025-12-12 17:24:15'),(55,1,'User Login','user',1,'27.49.10.164','2025-12-12 17:27:35'),(56,2,'User Login','user',2,'112.204.163.207','2025-12-12 17:29:01'),(57,1,'User Login','user',1,'158.62.18.221','2025-12-12 17:30:11'),(58,3,'User Login','user',3,'112.204.163.207','2025-12-12 17:32:44'),(59,3,'User Login','user',3,'112.204.163.207','2025-12-12 17:43:38'),(60,1,'User Login','user',1,'158.62.18.221','2025-12-12 17:47:38'),(61,3,'User Login','user',3,'158.62.18.221','2025-12-12 17:59:07'),(62,1,'User Login','user',1,'112.204.163.207','2025-12-12 17:59:17'),(63,2,'User Login','user',2,'112.204.163.207','2025-12-12 18:00:51'),(64,1,'User Login','user',1,'112.204.163.207','2025-12-12 18:02:56'),(65,6,'User Login','user',6,'158.62.18.221','2025-12-12 18:28:26'),(66,2,'User Login','user',2,'112.204.163.207','2025-12-12 18:50:59'),(67,1,'User Login','user',1,'136.158.10.168','2025-12-13 00:53:38'),(68,1,'User Login','user',1,'136.158.10.168','2025-12-13 01:04:39'),(69,3,'User Login','user',3,'136.158.10.168','2025-12-13 01:43:05'),(70,6,'User Login','user',6,'136.158.10.168','2025-12-13 02:12:57'),(71,5,'User Login','user',5,'136.158.10.168','2025-12-13 02:25:00'),(72,1,'User Login','user',1,'136.158.10.168','2025-12-13 02:49:21'),(73,1,'User Login','user',1,'136.158.10.168','2025-12-13 03:11:02'),(74,3,'User Login','user',3,'136.158.10.168','2025-12-13 03:14:02'),(75,6,'User Login','user',6,'136.158.10.168','2025-12-13 03:16:02'),(76,5,'User Login','user',5,'136.158.10.168','2025-12-13 03:17:24'),(77,3,'User Login','user',3,'136.158.10.168','2025-12-13 03:21:38'),(78,1,'User Login','user',1,'136.158.10.168','2025-12-13 03:22:48'),(79,6,'User Login','user',6,'136.158.10.168','2025-12-13 03:23:38'),(80,3,'User Login','user',3,'136.158.10.168','2025-12-13 03:24:11'),(81,6,'User Login','user',6,'136.158.10.168','2025-12-13 03:39:05'),(82,1,'User Login','user',1,'136.158.10.168','2025-12-13 03:47:52'),(83,1,'User Login','user',1,'27.49.10.164','2025-12-13 05:05:20'),(84,1,'User Login','user',1,'158.62.18.221','2025-12-13 05:05:38'),(85,3,'User Login','user',3,'27.49.10.164','2025-12-13 05:20:35'),(86,3,'User Login','user',3,'158.62.18.221','2025-12-13 05:22:38'),(87,6,'User Login','user',6,'158.62.18.221','2025-12-13 05:44:38'),(88,6,'User Login','user',6,'136.158.10.168','2025-12-13 06:03:21'),(89,5,'User Login','user',5,'136.158.10.168','2025-12-13 06:12:11'),(90,1,'User Login','user',1,'136.158.10.168','2025-12-13 06:14:07'),(91,2,'User Login','user',2,'136.158.11.82','2026-01-12 04:09:31'),(92,1,'User Login','user',1,'136.158.11.82','2026-01-12 04:09:50'),(93,3,'User Login','user',3,'136.158.11.82','2026-01-12 04:14:38'),(94,6,'User Login','user',6,'136.158.11.82','2026-01-12 04:26:13'),(95,1,'User Login','user',1,'27.49.10.90','2026-01-12 08:36:40'),(96,1,'User Login','user',1,'::1','2026-01-12 08:40:20'),(97,3,'User Login','user',3,'::1','2026-01-12 09:21:22');
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dean_list`
--

DROP TABLE IF EXISTS `dean_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dean_list` (
  `list_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `stud_id` int NOT NULL,
  `academic_year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') COLLATE utf8mb4_general_ci NOT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year') COLLATE utf8mb4_general_ci NOT NULL,
  `gpa` decimal(3,2) NOT NULL,
  `qpa` decimal(3,2) DEFAULT NULL,
  `status` enum('Pending','Under Review','Verified','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_general_ci,
  `verified_by` int DEFAULT NULL,
  `verified_date` timestamp NULL DEFAULT NULL,
  `submitted_by` int DEFAULT NULL,
  `submitted_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`list_id`),
  UNIQUE KEY `uniq_deanslist` (`stud_id`,`academic_year`,`semester`),
  KEY `verified_by` (`verified_by`),
  KEY `submitted_by` (`submitted_by`),
  KEY `idx_deans_list` (`student_id`,`academic_year`,`semester`),
  KEY `idx_stud_id` (`stud_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `dean_list_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `dean_list_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `dean_list_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dean_list`
--

LOCK TABLES `dean_list` WRITE;
/*!40000 ALTER TABLE `dean_list` DISABLE KEYS */;
INSERT INTO `dean_list` VALUES (1,4,20250001,'2024-2025','2nd Semester','3rd Year',4.00,4.00,'Pending',NULL,3,'2025-11-15 09:57:26',NULL,NULL,'2025-11-15 09:57:26'),(2,5,20250002,'2024-2025','2nd Semester','3rd Year',3.95,3.95,'Verified',NULL,3,'2025-11-15 09:57:26',NULL,NULL,'2025-11-15 09:57:26'),(3,4,20250001,'2024-2025','1st Semester','3rd Year',3.92,3.92,'Verified',NULL,3,'2025-05-15 09:57:26',NULL,NULL,'2025-11-15 09:57:26'),(4,4,20250001,'2025-2026','1st Semester','4th Year',4.00,4.00,'Verified','',3,'2025-11-16 07:32:57',NULL,NULL,'2025-11-15 09:57:26'),(5,5,20250002,'2025-2026','1st Semester','4th Year',3.87,3.87,'Under Review',NULL,NULL,NULL,NULL,NULL,'2025-11-15 09:57:26'),(6,10,20250005,'2025-2026','1st Semester','2nd Year',4.00,NULL,'Pending','',1,'2025-12-12 17:00:30',NULL,NULL,'2025-12-12 17:00:30'),(7,9,20250004,'2025-2026','1st Semester','3rd Year',3.20,NULL,'Pending','',1,'2025-12-12 17:04:00',NULL,NULL,'2025-12-12 17:04:00'),(8,9,20250004,'2025-2026','2nd Semester','3rd Year',3.20,NULL,'Pending','',1,'2025-12-12 17:12:24',NULL,NULL,'2025-12-12 17:12:24'),(9,11,20250006,'2024-2025','2nd Semester','3rd Year',2.00,NULL,'Pending','',1,'2025-12-13 05:10:03',NULL,NULL,'2025-12-13 05:10:03');
/*!40000 ALTER TABLE `dean_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document` (
  `doc_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `stud_id` int NOT NULL,
  `doc_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `doc_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `doc_desc` text COLLATE utf8mb4_general_ci,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `related_type` enum('enrollment','dean_list','scholarship','general') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `related_id` int DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Declined') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `comments` text COLLATE utf8mb4_general_ci,
  `created_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'Student',
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` int DEFAULT NULL,
  `review_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`doc_id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_document` (`student_id`,`related_type`),
  KEY `idx_stud_id` (`stud_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `document_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `document_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document`
--

LOCK TABLES `document` WRITE;
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
INSERT INTO `document` VALUES (4,1,20250002,'undefined','20250002_957414.jpg','Enrollment Form','Enrollment','uploads/',NULL,21163,NULL,NULL,'Approved',NULL,'Admin','2025-12-12 14:35:07','2025-12-12 14:35:07',1,NULL,'2025-12-12 16:37:19'),(5,5,20250002,'undefined','20250002_747072.jpg','Certificate','asdasdasd','uploads/',NULL,21163,NULL,NULL,'Approved',NULL,'Admin','2025-12-12 15:00:10','2025-12-12 15:00:10',1,NULL,'2025-12-12 16:37:09'),(6,4,20250001,'undefined','20250001_245683.jpg','Certificate','asdasdas','uploads/',NULL,21163,NULL,NULL,'Pending',NULL,'Admin','2025-12-12 15:06:42','2025-12-12 15:06:42',1,NULL,'2025-12-12 16:37:34'),(7,4,20250001,'undefined','20250001_488688.jpg','Certificate','TTATATAT','uploads/',NULL,21163,NULL,NULL,'Approved','Test Remarks','Admin','2025-12-12 15:07:12','2025-12-12 15:07:12',1,'2025-12-12 16:27:18','2025-12-12 16:37:29'),(9,9,20250004,'undefined','20250004_208365.jpg','Enrollment Form','asdasdasdgew','uploads/',NULL,21163,NULL,NULL,'Approved',NULL,'Admin','2025-12-12 16:18:31','2025-12-12 16:18:31',1,NULL,'2025-12-12 16:37:41'),(10,5,20250002,'Test Document','20250002_102830.pdf','ID','ID OF TEST','uploads/',NULL,15110,NULL,NULL,'Pending',NULL,'Admin','2026-01-12 09:19:27','2026-01-12 09:19:27',NULL,NULL,'2026-01-12 09:19:27');
/*!40000 ALTER TABLE `document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_tags`
--

DROP TABLE IF EXISTS `document_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_tags` (
  `doc_tag_id` int NOT NULL AUTO_INCREMENT,
  `doc_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`doc_tag_id`),
  UNIQUE KEY `unique_doc_tag` (`doc_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `document_tags_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `document` (`doc_id`) ON DELETE CASCADE,
  CONSTRAINT `document_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_tags`
--

LOCK TABLES `document_tags` WRITE;
/*!40000 ALTER TABLE `document_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidance_assessment`
--

DROP TABLE IF EXISTS `guidance_assessment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guidance_assessment` (
  `assessment_id` int NOT NULL AUTO_INCREMENT,
  `app_id` int NOT NULL,
  `student_id` int NOT NULL,
  `assessed_by` int NOT NULL,
  `financial_need_score` int DEFAULT NULL COMMENT 'Score 1-10',
  `character_score` int DEFAULT NULL COMMENT 'Score 1-10',
  `leadership_score` int DEFAULT NULL COMMENT 'Score 1-10',
  `personal_circumstances` text COLLATE utf8mb4_unicode_ci,
  `assessment_notes` text COLLATE utf8mb4_unicode_ci,
  `overall_recommendation` enum('Strongly Recommended','Recommended','Conditionally Recommended','Not Recommended') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority_level` enum('High','Medium','Low') COLLATE utf8mb4_unicode_ci DEFAULT 'Medium',
  `recommended_amount` decimal(10,2) DEFAULT NULL,
  `assessment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assessment_id`),
  KEY `idx_app_id` (`app_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_assessed_by` (`assessed_by`),
  KEY `idx_overall_recommendation` (`overall_recommendation`),
  KEY `idx_priority` (`priority_level`),
  CONSTRAINT `guidance_assessment_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `scholarship_application` (`app_id`) ON DELETE CASCADE,
  CONSTRAINT `guidance_assessment_ibfk_2` FOREIGN KEY (`assessed_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidance_assessment`
--

LOCK TABLES `guidance_assessment` WRITE;
/*!40000 ALTER TABLE `guidance_assessment` DISABLE KEYS */;
INSERT INTO `guidance_assessment` VALUES (1,2,5,6,8,9,7,'Student demonstrates genuine financial need. Family income below threshold.','Excellent character references. Active in community service. Recommended for full scholarship.','Strongly Recommended','High',50000.00,'2025-11-16 22:38:21','2025-11-16 22:38:21');
/*!40000 ALTER TABLE `guidance_assessment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidance_interview`
--

DROP TABLE IF EXISTS `guidance_interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guidance_interview` (
  `interview_id` int NOT NULL AUTO_INCREMENT,
  `app_id` int NOT NULL,
  `student_id` int NOT NULL,
  `conducted_by` int NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time DEFAULT NULL,
  `interview_type` enum('Initial','Follow-up','Verification','Counseling') COLLATE utf8mb4_unicode_ci DEFAULT 'Initial',
  `interview_reason` text COLLATE utf8mb4_unicode_ci,
  `interview_findings` text COLLATE utf8mb4_unicode_ci,
  `verified_information` text COLLATE utf8mb4_unicode_ci,
  `student_demeanor` text COLLATE utf8mb4_unicode_ci,
  `follow_up_needed` tinyint(1) DEFAULT '0',
  `follow_up_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Scheduled','Completed','Cancelled','No Show') COLLATE utf8mb4_unicode_ci DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`interview_id`),
  KEY `idx_app_id` (`app_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_conducted_by` (`conducted_by`),
  KEY `idx_interview_date` (`interview_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `guidance_interview_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `scholarship_application` (`app_id`) ON DELETE CASCADE,
  CONSTRAINT `guidance_interview_ibfk_2` FOREIGN KEY (`conducted_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidance_interview`
--

LOCK TABLES `guidance_interview` WRITE;
/*!40000 ALTER TABLE `guidance_interview` DISABLE KEYS */;
/*!40000 ALTER TABLE `guidance_interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `profile_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `stud_id` int NOT NULL,
  `student_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `firstName` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `middleName` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci GENERATED ALWAYS AS (concat(`firstName`,_utf8mb4' ',ifnull(concat(`middleName`,_utf8mb4' '),_utf8mb4''),`lastName`)) STORED,
  `course` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contactNumber` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `stud_id` (`stud_id`),
  UNIQUE KEY `student_number` (`student_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_student_number` (`student_number`),
  KEY `idx_stud_id` (`stud_id`),
  KEY `idx_name` (`lastName`,`firstName`),
  CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` (`profile_id`, `user_id`, `stud_id`, `student_number`, `firstName`, `lastName`, `middleName`, `course`, `year_level`, `contactNumber`, `address`, `created_at`, `updated_at`) VALUES (1,4,20250001,'20250001','Ron Jacob','Rodanilla',NULL,'BSIT','4th Year','09123456789','Quezon City','2025-11-15 09:57:26','2025-11-15 09:57:26'),(2,5,20250002,'20250002','John','Doe',NULL,'BSIT','4th Year','09187654321','Marikina City','2025-11-15 09:57:26','2025-11-15 09:57:26'),(3,9,20250004,'20250004','Mark','Smith','','BSIT','3rd Year','0912431754','QC','2025-12-12 15:51:25','2025-12-12 15:51:25'),(4,10,20250005,'20250005','Maricar','Smith','','BSIT','2nd Year','0912431754','QC','2025-12-12 16:14:30','2025-12-12 16:14:30'),(5,11,20250006,'20250006','Maricar','john','','BSIT','3rd Year','09938876533','Quezon City','2025-12-13 05:09:15','2025-12-13 05:09:15');
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scholarship_application`
--

DROP TABLE IF EXISTS `scholarship_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scholarship_application` (
  `app_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `stud_id` int NOT NULL,
  `academic_year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') COLLATE utf8mb4_general_ci NOT NULL,
  `scholarship_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Submitted','Under Review','Recommended','Under Guidance Review','On Hold - Guidance Review','Endorsed by Guidance','Not Endorsed','Approved','Rejected','On Hold') COLLATE utf8mb4_general_ci DEFAULT 'Submitted',
  `remarks` text COLLATE utf8mb4_general_ci,
  `guidance_remarks` text COLLATE utf8mb4_general_ci,
  `guidance_recommendation` enum('Endorsed','Not Endorsed','Needs Interview','Pending') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `application_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` int DEFAULT NULL,
  `guidance_reviewed_by` int DEFAULT NULL,
  `review_date` timestamp NULL DEFAULT NULL,
  `guidance_review_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`app_id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_scholarship` (`student_id`,`academic_year`,`semester`),
  KEY `idx_stud_id` (`stud_id`),
  KEY `idx_status` (`status`),
  KEY `idx_guidance_status` (`status`,`guidance_recommendation`),
  KEY `idx_guidance_reviewed` (`guidance_reviewed_by`,`guidance_review_date`),
  CONSTRAINT `fk_guidance_reviewer` FOREIGN KEY (`guidance_reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `scholarship_application_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `scholarship_application_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scholarship_application`
--

LOCK TABLES `scholarship_application` WRITE;
/*!40000 ALTER TABLE `scholarship_application` DISABLE KEYS */;
INSERT INTO `scholarship_application` VALUES (1,4,20250001,'2025-2026','1st Semester','Dean\'s List Scholar','Approved',NULL,NULL,'Pending','2025-11-15 09:57:26',1,NULL,NULL,NULL),(2,5,20250002,'2025-2026','1st Semester','Academic Excellence','Under Review',NULL,NULL,'Pending','2025-11-15 09:57:26',NULL,NULL,NULL,NULL),(3,4,20250001,'2024-2025','2nd Semester','Dean\'s List Scholar','Approved',NULL,NULL,'Pending','2025-11-15 09:57:26',1,NULL,NULL,NULL),(4,5,20250002,'2025-2026','1st Semester','Need-Based Scholarship','Under Review','Recommended by Dean for Guidance review',NULL,'Pending','2025-11-16 22:38:21',1,NULL,'2025-12-12 14:48:12',NULL);
/*!40000 ALTER TABLE `scholarship_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stud_id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `course` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `year_level` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `qpa` decimal(3,2) DEFAULT NULL,
  `min_grade` decimal(3,2) DEFAULT NULL,
  `is_regular_student` tinyint(1) DEFAULT '1',
  `took_only_curriculum_courses` tinyint(1) DEFAULT '1',
  `has_incomplete_grade` tinyint(1) DEFAULT '0',
  `has_dropped_or_failed` tinyint(1) DEFAULT '0',
  `violated_rules` tinyint(1) DEFAULT '0',
  `attendance_percent` decimal(5,2) DEFAULT NULL,
  `eligible` tinyint(1) DEFAULT NULL,
  `eligibility_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `requirements` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stud_id` (`stud_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_stud_id` (`stud_id`),
  KEY `idx_student_name` (`name`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,20250001,4,'Ron Jacob Rodanilla','BSIT','4th Year',4.00,1.00,1,1,0,0,0,100.00,1,'Eligible',NULL,'2025-11-15 09:57:26','2025-11-15 09:57:26'),(2,20250002,5,'John Doe','BSIT','4th Year',3.95,1.00,1,1,0,0,0,98.00,1,'Eligible',NULL,'2025-11-15 09:57:26','2025-11-15 09:57:26');
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_enrollment`
--

DROP TABLE IF EXISTS `student_enrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_enrollment` (
  `enrollment_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `stud_id` int NOT NULL,
  `academic_year` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') COLLATE utf8mb4_general_ci NOT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year') COLLATE utf8mb4_general_ci NOT NULL,
  `course` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Enrolled','Withdrawn','Completed') COLLATE utf8mb4_general_ci DEFAULT 'Enrolled',
  `enrollment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`enrollment_id`),
  KEY `idx_enrollment` (`student_id`,`academic_year`,`semester`),
  KEY `idx_stud_id` (`stud_id`),
  CONSTRAINT `student_enrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_enrollment`
--

LOCK TABLES `student_enrollment` WRITE;
/*!40000 ALTER TABLE `student_enrollment` DISABLE KEYS */;
INSERT INTO `student_enrollment` VALUES (1,4,20250001,'2025-2026','1st Semester','4th Year','BSIT','Enrolled','2025-11-15 09:57:26'),(2,5,20250002,'2025-2026','1st Semester','4th Year','BSIT','Enrolled','2025-11-15 09:57:26'),(3,4,20250001,'2024-2025','2nd Semester','3rd Year','BSIT','Completed','2025-11-15 09:57:26'),(4,5,20250002,'2024-2025','2nd Semester','3rd Year','BSIT','Completed','2025-11-15 09:57:26');
/*!40000 ALTER TABLE `student_enrollment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (12,'Academic'),(6,'Approved'),(8,'Dean\'s List'),(7,'Declined'),(1,'Education'),(10,'Enrollment'),(3,'Finance'),(5,'Pending'),(4,'Personal'),(9,'Scholarship'),(2,'Urgent'),(11,'Verified');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `stud_id` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Student','Admin','Dean','Registrar','Guidance') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Active','Inactive','Suspended') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_stud_id` (`stud_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1001,'admin@feu.edu.ph','Admin','$2y$12$DmtqpYoAECYkrA89Zsx3au33xHdRYQGWEwksW5qrmWTJCdZCg12kq','Admin','Active','2025-11-15 09:57:26','2026-01-12 08:40:20'),(2,1002,'dean@feu.edu.ph','Dean','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Dean','Active','2025-11-15 09:57:26','2026-01-12 04:09:31'),(3,1003,'registrar@feu.edu.ph','Registrar','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Registrar','Active','2025-11-15 09:57:26','2026-01-12 09:21:22'),(4,20250001,'ronrodanilla@gmail.com','Ron','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Student','Active','2025-11-15 09:57:26','2025-11-15 10:01:33'),(5,20250002,'johndoe@gmail.com','John Doe','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Student','Active','2025-11-15 09:57:26','2025-12-13 06:12:11'),(6,1004,'guidance@feu.edu.ph','Guidance Office','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Guidance','Active','2025-11-16 20:21:00','2026-01-12 04:26:13'),(9,20250004,'Mark@mail.com','Mark','$2y$12$GXooIXG84EJi6CKCUZeQpeM4gDbqm19LU6ZLDRktw0T47pVjYmGfO','Student','Active','2025-12-12 15:51:25',NULL),(10,20250005,'Maricar@mail.com','Maricar','$2y$12$fDSOs6IjYQXqPX8Dd9u3T.Fkvn8vQJ8gd.JpdfbN9eYZzc00vcq.a','Student','Active','2025-12-12 16:14:30',NULL),(11,20250006,'roonroodanilla1@gmail.com','john','$2y$10$qMV/EZtJa3DXVh2hKqZF.uOzRTptT8oVTCxU1k9wbgZS42hY4YkEW','Student','Active','2025-12-13 05:09:15',NULL);
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

-- Dump completed on 2026-01-12 17:40:37
