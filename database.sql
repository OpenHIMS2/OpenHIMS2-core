-- MariaDB dump 10.19  Distrib 10.4.22-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: phims
-- ------------------------------------------------------
-- Server version	10.4.22-MariaDB

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
-- Table structure for table `blood_pressure_readings`
--

DROP TABLE IF EXISTS `blood_pressure_readings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blood_pressure_readings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `systolic` smallint(5) unsigned NOT NULL,
  `diastolic` smallint(5) unsigned NOT NULL,
  `recorded_at` datetime NOT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blood_pressure_readings_visit_id_foreign` (`visit_id`),
  KEY `blood_pressure_readings_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `blood_pressure_readings_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blood_pressure_readings_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blood_pressure_readings`
--

LOCK TABLES `blood_pressure_readings` WRITE;
/*!40000 ALTER TABLE `blood_pressure_readings` DISABLE KEYS */;
INSERT INTO `blood_pressure_readings` VALUES (1,1,162,98,'2025-11-01 09:00:00',33,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(2,2,162,98,'2025-11-02 10:00:00',33,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(3,3,155,94,'2025-11-22 09:05:00',33,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(4,4,155,94,'2025-11-23 10:05:00',33,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(5,5,148,90,'2025-12-13 09:10:00',33,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(6,6,148,90,'2025-12-14 10:10:00',33,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(7,7,142,88,'2026-01-03 09:15:00',33,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(8,8,142,88,'2026-01-04 10:15:00',33,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(9,9,138,86,'2026-01-24 09:20:00',33,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(10,10,138,86,'2026-01-25 10:20:00',33,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(11,11,148,92,'2025-11-08 09:00:00',33,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(12,12,148,92,'2025-11-09 10:00:00',33,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(13,13,145,90,'2025-11-29 09:05:00',33,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(14,14,145,90,'2025-11-30 10:05:00',33,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(15,15,142,88,'2025-12-20 09:10:00',33,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(16,16,142,88,'2025-12-21 10:10:00',33,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(17,17,140,86,'2026-01-10 09:15:00',33,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(18,18,140,86,'2026-01-11 10:15:00',33,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(19,19,138,84,'2026-01-31 09:20:00',33,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(20,20,138,84,'2026-02-01 10:20:00',33,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(21,21,140,88,'2025-11-15 09:00:00',33,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(22,22,140,88,'2025-11-16 10:00:00',33,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(23,23,138,86,'2025-12-06 09:05:00',33,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(24,24,138,86,'2025-12-07 10:05:00',33,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(25,25,136,84,'2025-12-27 09:10:00',33,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(26,26,136,84,'2025-12-28 10:10:00',33,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(27,27,134,82,'2026-01-17 09:15:00',33,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(28,28,134,82,'2026-01-18 10:15:00',33,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(29,29,132,80,'2026-02-07 09:20:00',33,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(30,30,132,80,'2026-02-08 10:20:00',33,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(31,31,118,76,'2025-11-22 09:00:00',33,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(32,32,118,76,'2025-11-23 10:00:00',33,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(33,33,116,74,'2025-12-13 09:05:00',33,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(34,34,116,74,'2025-12-14 10:05:00',33,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(35,35,118,76,'2026-01-03 09:10:00',33,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(36,36,118,76,'2026-01-04 10:10:00',33,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(37,37,120,78,'2026-01-24 09:15:00',33,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(38,38,120,78,'2026-01-25 10:15:00',33,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(39,39,118,76,'2026-02-14 09:20:00',33,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(40,40,118,76,'2026-02-15 10:20:00',33,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(41,41,158,98,'2025-11-29 09:00:00',33,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(42,42,158,98,'2025-11-30 10:00:00',33,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(43,43,152,94,'2025-12-20 09:05:00',33,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(44,44,152,94,'2025-12-21 10:05:00',33,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(45,45,148,92,'2026-01-10 09:10:00',33,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(46,46,148,92,'2026-01-11 10:10:00',33,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(47,47,145,90,'2026-01-31 09:15:00',33,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(48,48,145,90,'2026-02-01 10:15:00',33,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(49,49,142,88,'2026-02-21 09:20:00',33,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(50,50,142,88,'2026-02-22 10:20:00',33,'2026-04-21 11:06:48','2026-04-21 11:06:48');
/*!40000 ALTER TABLE `blood_pressure_readings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinic_visits`
--

DROP TABLE IF EXISTS `clinic_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clinic_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_number` smallint(5) unsigned NOT NULL,
  `queue_session` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `category` enum('opd','new_clinic_visit','recurrent_clinic_visit','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'opd',
  `opd_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` decimal(5,1) DEFAULT NULL,
  `weight` decimal(5,1) DEFAULT NULL,
  `bp_systolic` smallint(5) unsigned DEFAULT NULL,
  `bp_diastolic` smallint(5) unsigned DEFAULT NULL,
  `clinic_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','in_progress','visited','cancelled','dispensed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `registered_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cv_unit_date_cat_sess_num_unique` (`unit_id`,`visit_date`,`category`,`queue_session`,`visit_number`),
  KEY `clinic_visits_registered_by_foreign` (`registered_by`),
  KEY `cv_unit_date_status` (`unit_id`,`visit_date`,`status`),
  KEY `cv_patient_date` (`patient_id`,`visit_date`),
  KEY `cv_institution_date` (`institution_id`,`visit_date`),
  KEY `cv_visit_date` (`visit_date`),
  CONSTRAINT `clinic_visits_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clinic_visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clinic_visits_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`),
  CONSTRAINT `clinic_visits_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinic_visits`
--

LOCK TABLES `clinic_visits` WRITE;
/*!40000 ALTER TABLE `clinic_visits` DISABLE KEYS */;
INSERT INTO `clinic_visits` VALUES (1,1,1,3,'2025-11-01',11,1,'opd','OPD-1001',168.0,87.5,162,98,NULL,'visited',32,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(2,1,1,3,'2025-11-02',12,1,'new_clinic_visit',NULL,168.0,87.5,162,98,'CLN-A-2401','visited',32,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(3,1,1,3,'2025-11-22',21,1,'opd','OPD-1022',168.0,86.0,155,94,NULL,'visited',32,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(4,1,1,3,'2025-11-23',22,1,'recurrent_clinic_visit',NULL,168.0,86.0,155,94,'CLN-A-2402','visited',32,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(5,1,1,3,'2025-12-13',31,1,'opd','OPD-1045',168.0,84.5,148,90,NULL,'visited',32,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(6,1,1,3,'2025-12-14',32,1,'recurrent_clinic_visit',NULL,168.0,84.5,148,90,'CLN-A-2403','visited',32,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(7,1,1,3,'2026-01-03',41,1,'opd','OPD-1067',168.0,83.0,142,88,NULL,'visited',32,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(8,1,1,3,'2026-01-04',42,1,'recurrent_clinic_visit',NULL,168.0,83.0,142,88,'CLN-A-2404','visited',32,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(9,1,1,3,'2026-01-24',51,1,'opd','OPD-1089',168.0,82.0,138,86,NULL,'visited',32,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(10,1,1,3,'2026-01-25',52,1,'recurrent_clinic_visit',NULL,168.0,82.0,138,86,'CLN-A-2405','visited',32,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(11,2,1,3,'2025-11-08',1011,1,'opd','OPD-2001',158.0,68.0,148,92,NULL,'visited',32,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(12,2,1,3,'2025-11-09',1012,1,'new_clinic_visit',NULL,158.0,68.0,148,92,'CLN-B-2401','visited',32,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(13,2,1,3,'2025-11-29',1021,1,'opd','OPD-2015',158.0,67.5,145,90,NULL,'visited',32,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(14,2,1,3,'2025-11-30',1022,1,'recurrent_clinic_visit',NULL,158.0,67.5,145,90,'CLN-B-2402','visited',32,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(15,2,1,3,'2025-12-20',1031,1,'opd','OPD-2030',158.0,67.0,142,88,NULL,'visited',32,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(16,2,1,3,'2025-12-21',1032,1,'recurrent_clinic_visit',NULL,158.0,67.0,142,88,'CLN-B-2403','visited',32,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(17,2,1,3,'2026-01-10',1041,1,'opd','OPD-2044',158.0,66.5,140,86,NULL,'visited',32,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(18,2,1,3,'2026-01-11',1042,1,'recurrent_clinic_visit',NULL,158.0,66.5,140,86,'CLN-B-2404','visited',32,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(19,2,1,3,'2026-01-31',1051,1,'opd','OPD-2059',158.0,66.0,138,84,NULL,'visited',32,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(20,2,1,3,'2026-02-01',1052,1,'recurrent_clinic_visit',NULL,158.0,66.0,138,84,'CLN-B-2405','visited',32,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(21,3,1,3,'2025-11-15',2011,1,'opd','OPD-3001',172.0,78.0,140,88,NULL,'visited',32,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(22,3,1,3,'2025-11-16',2012,1,'new_clinic_visit',NULL,172.0,78.0,140,88,'CLN-C-2401','visited',32,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(23,3,1,3,'2025-12-06',2021,1,'opd','OPD-3018',172.0,78.5,138,86,NULL,'visited',32,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(24,3,1,3,'2025-12-07',2022,1,'recurrent_clinic_visit',NULL,172.0,78.5,138,86,'CLN-C-2402','visited',32,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(25,3,1,3,'2025-12-27',2031,1,'opd','OPD-3035',172.0,77.5,136,84,NULL,'visited',32,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(26,3,1,3,'2025-12-28',2032,1,'recurrent_clinic_visit',NULL,172.0,77.5,136,84,'CLN-C-2403','visited',32,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(27,3,1,3,'2026-01-17',2041,1,'opd','OPD-3052',172.0,77.0,134,82,NULL,'visited',32,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(28,3,1,3,'2026-01-18',2042,1,'recurrent_clinic_visit',NULL,172.0,77.0,134,82,'CLN-C-2404','visited',32,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(29,3,1,3,'2026-02-07',2051,1,'opd','OPD-3069',172.0,76.5,132,80,NULL,'visited',32,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(30,3,1,3,'2026-02-08',2052,1,'recurrent_clinic_visit',NULL,172.0,76.5,132,80,'CLN-C-2405','visited',32,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(31,4,1,3,'2025-11-22',3011,1,'opd','OPD-4001',163.0,58.0,118,76,NULL,'visited',32,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(32,4,1,3,'2025-11-23',3012,1,'new_clinic_visit',NULL,163.0,58.0,118,76,'CLN-D-2401','visited',32,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(33,4,1,3,'2025-12-13',3021,1,'opd','OPD-4010',163.0,57.5,116,74,NULL,'visited',32,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(34,4,1,3,'2025-12-14',3022,1,'recurrent_clinic_visit',NULL,163.0,57.5,116,74,'CLN-D-2402','visited',32,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(35,4,1,3,'2026-01-03',3031,1,'opd','OPD-4022',163.0,57.5,118,76,NULL,'visited',32,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(36,4,1,3,'2026-01-04',3032,1,'recurrent_clinic_visit',NULL,163.0,57.5,118,76,'CLN-D-2403','visited',32,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(37,4,1,3,'2026-01-24',3041,1,'opd','OPD-4035',163.0,58.0,120,78,NULL,'visited',32,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(38,4,1,3,'2026-01-25',3042,1,'recurrent_clinic_visit',NULL,163.0,58.0,120,78,'CLN-D-2404','visited',32,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(39,4,1,3,'2026-02-14',3051,1,'opd','OPD-4048',163.0,58.0,118,76,NULL,'visited',32,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(40,4,1,3,'2026-02-15',3052,1,'recurrent_clinic_visit',NULL,163.0,58.0,118,76,'CLN-D-2405','visited',32,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(41,5,1,3,'2025-11-29',4011,1,'opd','OPD-5001',170.0,72.0,158,98,NULL,'visited',32,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(42,5,1,3,'2025-11-30',4012,1,'new_clinic_visit',NULL,170.0,72.0,158,98,'CLN-E-2401','visited',32,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(43,5,1,3,'2025-12-20',4021,1,'opd','OPD-5020',170.0,71.5,152,94,NULL,'visited',32,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(44,5,1,3,'2025-12-21',4022,1,'recurrent_clinic_visit',NULL,170.0,71.5,152,94,'CLN-E-2402','visited',32,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(45,5,1,3,'2026-01-10',4031,1,'opd','OPD-5039',170.0,71.0,148,92,NULL,'visited',32,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(46,5,1,3,'2026-01-11',4032,1,'recurrent_clinic_visit',NULL,170.0,71.0,148,92,'CLN-E-2403','visited',32,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(47,5,1,3,'2026-01-31',4041,1,'opd','OPD-5058',170.0,70.5,145,90,NULL,'visited',32,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(48,5,1,3,'2026-02-01',4042,1,'recurrent_clinic_visit',NULL,170.0,70.5,145,90,'CLN-E-2404','visited',32,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(49,5,1,3,'2026-02-21',4051,1,'opd','OPD-5077',170.0,70.0,142,88,NULL,'visited',32,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(50,5,1,3,'2026-02-22',4052,1,'recurrent_clinic_visit',NULL,170.0,70.0,142,88,'CLN-E-2405','visited',32,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(51,5,1,3,'2026-04-21',1,1,'recurrent_clinic_visit',NULL,NULL,NULL,NULL,NULL,'NH/NR/GH/21/04/26/1/3/001','dispensed',37,'2026-04-21 11:38:24','2026-04-21 11:44:37');
/*!40000 ALTER TABLE `clinic_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drug_name_defaults`
--

DROP TABLE IF EXISTS `drug_name_defaults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drug_name_defaults` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `drug_name_id` bigint(20) unsigned NOT NULL,
  `type` enum('Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Oral',
  `dose` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` enum('mg','g','mcg','ml','tabs','item') COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` enum('mane','nocte','bd','tds','daily','EOD','SOS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_name_defaults_drug_name_id_unique` (`drug_name_id`),
  CONSTRAINT `drug_name_defaults_drug_name_id_foreign` FOREIGN KEY (`drug_name_id`) REFERENCES `drug_names` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drug_name_defaults`
--

LOCK TABLES `drug_name_defaults` WRITE;
/*!40000 ALTER TABLE `drug_name_defaults` DISABLE KEYS */;
INSERT INTO `drug_name_defaults` VALUES (1,1,'Oral','500','mg','bd','30 days','2026-04-21 11:06:26','2026-04-21 11:06:26'),(2,2,'Oral','5','mg','mane','30 days','2026-04-21 11:06:26','2026-04-21 11:06:26'),(3,3,'Oral','5','mg','mane','30 days','2026-04-21 11:06:26','2026-04-21 11:06:26'),(4,4,'Oral','100','mg','mane','30 days','2026-04-21 11:06:26','2026-04-21 11:06:26'),(5,5,'Oral','10','mg','mane','30 days','2026-04-21 11:06:26','2026-04-21 11:06:26'),(6,6,'Oral','5','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(7,7,'Oral','5','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(8,8,'Oral','5','mg','bd','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(9,9,'Oral','50','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(10,10,'Oral','80','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(11,11,'Oral','25','mg','bd','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(12,12,'Oral','25','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(13,13,'Oral','40','mg','mane','14 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(14,14,'Oral','25','mg','mane','30 days','2026-04-21 11:06:27','2026-04-21 11:06:27'),(15,15,'Oral','20','mg','nocte','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(16,16,'Oral','20','mg','nocte','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(17,17,'Oral','10','mg','nocte','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(18,18,'Oral','75','mg','mane','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(19,19,'Oral','75','mg','mane','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(20,20,'Oral','5','mg','mane','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(21,21,'Oral','0.25','mg','mane','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(22,22,'MDI','100','mcg','SOS',NULL,'2026-04-21 11:06:28','2026-04-21 11:06:28'),(23,23,'MDI','200','mcg','bd','30 days','2026-04-21 11:06:28','2026-04-21 11:06:28'),(24,24,'Oral','200','mg','bd','30 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(25,25,'Oral','20','mg','bd','14 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(26,26,'Oral','40','mg','mane','14 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(27,27,'Oral','150','mg','bd','14 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(28,28,'Oral','10','mg','tds','7 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(29,29,'Oral','4','mg','tds','5 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(30,30,'Oral','400','mg','tds','7 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(31,31,'Oral','500','mg','tds','5 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(32,32,'Oral','400','mg','tds','5 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(33,33,'Oral','50','mg','tds','5 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(34,34,'Oral','5','mg','mane','14 days','2026-04-21 11:06:29','2026-04-21 11:06:29'),(35,35,'IM','4','mg','SOS',NULL,'2026-04-21 11:06:29','2026-04-21 11:06:29'),(36,36,'Oral','0.5','mg','bd','5 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(37,37,'Oral','100','mg','mane','30 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(38,38,'Oral','500','mg','tds','7 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(39,39,'Oral','500','mg','bd','7 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(40,40,'Oral','960','mg','bd','7 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(41,41,'Oral','100','mg','bd','7 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(42,42,'Oral','500','mg','mane','5 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(43,43,'IV','1','g','mane','7 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(44,44,'Oral','50','mcg','mane','30 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(45,45,'Oral','25','mg','nocte','30 days','2026-04-21 11:06:30','2026-04-21 11:06:30'),(46,46,'Oral','50','mg','mane','30 days','2026-04-21 11:06:31','2026-04-21 11:06:31'),(47,47,'Oral','300','mg','tds','30 days','2026-04-21 11:06:31','2026-04-21 11:06:31'),(48,48,'Oral','200','mg','bd','30 days','2026-04-21 11:06:31','2026-04-21 11:06:31'),(49,49,'Oral','100','mg','bd','30 days','2026-04-21 11:06:31','2026-04-21 11:06:31'),(50,50,'Oral','5','mg','nocte','7 days','2026-04-21 11:06:31','2026-04-21 11:06:31');
/*!40000 ALTER TABLE `drug_name_defaults` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drug_names`
--

DROP TABLE IF EXISTS `drug_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drug_names` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_names_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drug_names`
--

LOCK TABLES `drug_names` WRITE;
/*!40000 ALTER TABLE `drug_names` DISABLE KEYS */;
INSERT INTO `drug_names` VALUES (1,'Metformin','2026-04-21 11:06:26','2026-04-21 11:06:26'),(2,'Glibenclamide','2026-04-21 11:06:26','2026-04-21 11:06:26'),(3,'Glipizide','2026-04-21 11:06:26','2026-04-21 11:06:26'),(4,'Sitagliptin','2026-04-21 11:06:26','2026-04-21 11:06:26'),(5,'Empagliflozin','2026-04-21 11:06:26','2026-04-21 11:06:26'),(6,'Amlodipine','2026-04-21 11:06:26','2026-04-21 11:06:26'),(7,'Lisinopril','2026-04-21 11:06:27','2026-04-21 11:06:27'),(8,'Enalapril','2026-04-21 11:06:27','2026-04-21 11:06:27'),(9,'Losartan','2026-04-21 11:06:27','2026-04-21 11:06:27'),(10,'Valsartan','2026-04-21 11:06:27','2026-04-21 11:06:27'),(11,'Metoprolol','2026-04-21 11:06:27','2026-04-21 11:06:27'),(12,'Hydrochlorothiazide','2026-04-21 11:06:27','2026-04-21 11:06:27'),(13,'Furosemide','2026-04-21 11:06:27','2026-04-21 11:06:27'),(14,'Spironolactone','2026-04-21 11:06:27','2026-04-21 11:06:27'),(15,'Atorvastatin','2026-04-21 11:06:27','2026-04-21 11:06:27'),(16,'Simvastatin','2026-04-21 11:06:28','2026-04-21 11:06:28'),(17,'Rosuvastatin','2026-04-21 11:06:28','2026-04-21 11:06:28'),(18,'Aspirin','2026-04-21 11:06:28','2026-04-21 11:06:28'),(19,'Clopidogrel','2026-04-21 11:06:28','2026-04-21 11:06:28'),(20,'Warfarin','2026-04-21 11:06:28','2026-04-21 11:06:28'),(21,'Digoxin','2026-04-21 11:06:28','2026-04-21 11:06:28'),(22,'Salbutamol','2026-04-21 11:06:28','2026-04-21 11:06:28'),(23,'Beclomethasone','2026-04-21 11:06:28','2026-04-21 11:06:28'),(24,'Theophylline','2026-04-21 11:06:29','2026-04-21 11:06:29'),(25,'Omeprazole','2026-04-21 11:06:29','2026-04-21 11:06:29'),(26,'Pantoprazole','2026-04-21 11:06:29','2026-04-21 11:06:29'),(27,'Ranitidine','2026-04-21 11:06:29','2026-04-21 11:06:29'),(28,'Domperidone','2026-04-21 11:06:29','2026-04-21 11:06:29'),(29,'Ondansetron','2026-04-21 11:06:29','2026-04-21 11:06:29'),(30,'Metronidazole','2026-04-21 11:06:29','2026-04-21 11:06:29'),(31,'Paracetamol','2026-04-21 11:06:29','2026-04-21 11:06:29'),(32,'Ibuprofen','2026-04-21 11:06:29','2026-04-21 11:06:29'),(33,'Tramadol','2026-04-21 11:06:29','2026-04-21 11:06:29'),(34,'Prednisolone','2026-04-21 11:06:29','2026-04-21 11:06:29'),(35,'Dexamethasone','2026-04-21 11:06:29','2026-04-21 11:06:29'),(36,'Colchicine','2026-04-21 11:06:30','2026-04-21 11:06:30'),(37,'Allopurinol','2026-04-21 11:06:30','2026-04-21 11:06:30'),(38,'Amoxicillin','2026-04-21 11:06:30','2026-04-21 11:06:30'),(39,'Ciprofloxacin','2026-04-21 11:06:30','2026-04-21 11:06:30'),(40,'Cotrimoxazole','2026-04-21 11:06:30','2026-04-21 11:06:30'),(41,'Doxycycline','2026-04-21 11:06:30','2026-04-21 11:06:30'),(42,'Azithromycin','2026-04-21 11:06:30','2026-04-21 11:06:30'),(43,'Ceftriaxone','2026-04-21 11:06:30','2026-04-21 11:06:30'),(44,'Levothyroxine','2026-04-21 11:06:30','2026-04-21 11:06:30'),(45,'Amitriptyline','2026-04-21 11:06:30','2026-04-21 11:06:30'),(46,'Sertraline','2026-04-21 11:06:30','2026-04-21 11:06:30'),(47,'Gabapentin','2026-04-21 11:06:31','2026-04-21 11:06:31'),(48,'Carbamazepine','2026-04-21 11:06:31','2026-04-21 11:06:31'),(49,'Phenytoin','2026-04-21 11:06:31','2026-04-21 11:06:31'),(50,'Diazepam','2026-04-21 11:06:31','2026-04-21 11:06:31');
/*!40000 ALTER TABLE `drug_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institutions_parent_id_foreign` (`parent_id`),
  CONSTRAINT `institutions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES (1,'National Department of Health','NH',NULL,NULL,NULL,NULL,NULL,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(2,'Northern Regional Health Authority','NR',NULL,NULL,NULL,NULL,1,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(3,'St. George\'s General Hospital','GH',NULL,NULL,NULL,NULL,2,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(4,'Riverside Community Hospital','RC',NULL,NULL,NULL,NULL,2,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(5,'Southern Regional Health Authority','SR',NULL,NULL,NULL,NULL,1,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(6,'Westfield Teaching Hospital','WT',NULL,NULL,NULL,NULL,5,'2026-04-21 11:06:18','2026-04-21 11:06:18');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `investigations`
--

DROP TABLE IF EXISTS `investigations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `investigations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recorded_at` datetime NOT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `investigations_visit_id_foreign` (`visit_id`),
  KEY `investigations_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `investigations_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `investigations_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `investigations`
--

LOCK TABLES `investigations` WRITE;
/*!40000 ALTER TABLE `investigations` DISABLE KEYS */;
INSERT INTO `investigations` VALUES (1,2,'HbA1c','7.8%','2025-11-02 10:30:00',31,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(2,2,'Serum Creatinine','90 µmol/L','2025-11-02 10:32:00',31,'2026-04-21 11:06:32','2026-04-21 11:06:32'),(3,4,'HbA1c','7.6%','2025-11-23 10:30:00',31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(4,4,'Serum Creatinine','88 µmol/L','2025-11-23 10:32:00',31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(5,6,'HbA1c','7.4%','2025-12-14 10:30:00',31,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(6,6,'Serum Creatinine','86 µmol/L','2025-12-14 10:32:00',31,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(7,8,'HbA1c','7.2%','2026-01-04 10:30:00',31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(8,8,'Serum Creatinine','84 µmol/L','2026-01-04 10:32:00',31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(9,10,'HbA1c','7.0%','2026-01-25 10:30:00',31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(10,10,'Serum Creatinine','82 µmol/L','2026-01-25 10:32:00',31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(11,12,'HbA1c','7.7%','2025-11-09 10:30:00',31,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(12,12,'Serum Creatinine','105 µmol/L','2025-11-09 10:32:00',31,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(13,14,'HbA1c','7.5%','2025-11-30 10:30:00',31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(14,14,'Serum Creatinine','103 µmol/L','2025-11-30 10:32:00',31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(15,16,'HbA1c','7.3%','2025-12-21 10:30:00',31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(16,16,'Serum Creatinine','101 µmol/L','2025-12-21 10:32:00',31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(17,18,'HbA1c','7.1%','2026-01-11 10:30:00',31,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(18,18,'Serum Creatinine','99 µmol/L','2026-01-11 10:32:00',31,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(19,20,'HbA1c','6.9%','2026-02-01 10:30:00',31,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(20,20,'Serum Creatinine','97 µmol/L','2026-02-01 10:32:00',31,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(21,22,'HbA1c','7.6%','2025-11-16 10:30:00',31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(22,24,'HbA1c','7.4%','2025-12-07 10:30:00',31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(23,26,'HbA1c','7.2%','2025-12-28 10:30:00',31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(24,28,'HbA1c','7.0%','2026-01-18 10:30:00',31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(25,30,'HbA1c','6.8%','2026-02-08 10:30:00',31,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(26,32,'HbA1c','7.5%','2025-11-23 10:30:00',31,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(27,34,'HbA1c','7.3%','2025-12-14 10:30:00',31,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(28,36,'HbA1c','7.1%','2026-01-04 10:30:00',31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(29,38,'HbA1c','6.9%','2026-01-25 10:30:00',31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(30,40,'HbA1c','6.7%','2026-02-15 10:30:00',31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(31,42,'HbA1c','7.4%','2025-11-30 10:30:00',31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(32,42,'Serum Creatinine','150 µmol/L','2025-11-30 10:32:00',31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(33,44,'HbA1c','7.2%','2025-12-21 10:30:00',31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(34,44,'Serum Creatinine','148 µmol/L','2025-12-21 10:32:00',31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(35,46,'HbA1c','7.0%','2026-01-11 10:30:00',31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(36,46,'Serum Creatinine','146 µmol/L','2026-01-11 10:32:00',31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(37,48,'HbA1c','6.8%','2026-02-01 10:30:00',31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(38,48,'Serum Creatinine','144 µmol/L','2026-02-01 10:32:00',31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(39,50,'HbA1c','6.6%','2026-02-22 10:30:00',31,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(40,50,'Serum Creatinine','142 µmol/L','2026-02-22 10:32:00',31,'2026-04-21 11:06:48','2026-04-21 11:06:48');
/*!40000 ALTER TABLE `investigations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_01_01_100000_create_institutions_table',1),(6,'2024_01_01_200000_create_unit_templates_table',1),(7,'2024_01_01_300000_create_view_templates_table',1),(8,'2024_01_01_400000_create_units_table',1),(9,'2024_01_01_500000_create_unit_views_table',1),(10,'2024_01_01_600000_add_role_institution_to_users_table',1),(11,'2024_01_01_700000_create_user_units_table',1),(12,'2024_01_01_800000_create_user_views_table',1),(13,'2024_01_02_000000_update_office_and_gp_view_templates',1),(14,'2024_01_03_100000_create_patients_table',1),(15,'2024_01_03_200000_create_clinic_visits_table',1),(16,'2024_01_03_300000_add_category_to_clinic_visits_table',1),(17,'2024_01_03_400000_update_clinic_visits_unique_per_category',1),(18,'2024_01_03_500000_add_queue_started_at_to_units',1),(19,'2024_01_03_600000_add_cancelled_to_clinic_visits_status',1),(20,'2024_01_03_700000_add_queue_session_to_clinic_visits',1),(21,'2026_02_23_161020_add_address_to_patients_table',1),(22,'2026_02_24_100000_add_visit_details_to_clinic_visits_table',1),(23,'2026_02_25_000000_update_category_enum_in_clinic_visits',2),(24,'2026_02_25_100000_create_terminology_terms_table',3),(25,'2026_03_03_000000_rename_staff_to_urgent_in_clinic_visits',4),(26,'2026_03_26_100000_create_patient_allergies_table',5),(27,'2026_03_26_200000_create_visit_notes_table',5),(28,'2026_03_26_300000_add_examination_to_visit_notes_table',6),(29,'2026_03_26_400000_create_blood_pressure_readings_table',6),(30,'2026_03_26_500000_create_investigations_table',7),(31,'2026_03_26_600000_create_visit_drugs_table',8),(32,'2026_03_26_700000_create_drug_names_table',9),(33,'2026_03_27_100000_add_section_duration_to_visit_drugs_table',10),(34,'2026_03_27_200000_add_management_instruction_to_visit_notes_table',10),(35,'2026_03_28_100000_add_profile_fields_to_users_table',11),(36,'2026_04_01_100000_add_code_to_institutions_table',12),(37,'2026_04_01_200000_change_institution_code_length',13),(38,'2026_04_06_100000_add_unit_number_to_units_table',14),(39,'2026_04_06_200000_optimize_clinic_visits_indexes',15),(40,'2026_04_06_300000_add_duration_to_drug_name_defaults_table',16),(41,'2026_04_07_100000_create_pharmacy_stock_table',17),(42,'2026_04_07_200000_create_prescription_dispensings_table',17),(43,'2026_04_07_300000_add_dispensed_to_clinic_visits_status',17),(44,'2026_04_14_100000_create_pharmacy_restock_logs_table',18),(45,'2026_04_21_100000_add_contact_fields_to_institutions_table',19),(46,'2024_01_10_000000_add_is_system_to_templates_tables',20),(47,'2024_02_01_000000_create_terminology_categories_table',21);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient_allergies`
--

DROP TABLE IF EXISTS `patient_allergies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_allergies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) unsigned NOT NULL,
  `allergen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_allergies_patient_id_allergen_unique` (`patient_id`,`allergen`),
  CONSTRAINT `patient_allergies_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient_allergies`
--

LOCK TABLES `patient_allergies` WRITE;
/*!40000 ALTER TABLE `patient_allergies` DISABLE KEYS */;
INSERT INTO `patient_allergies` VALUES (1,1,'Penicillin','2026-04-21 11:06:31','2026-04-21 11:06:31'),(2,2,'Sulfonamides','2026-04-21 11:06:36','2026-04-21 11:06:36'),(3,4,'Aspirin','2026-04-21 11:06:43','2026-04-21 11:06:43'),(4,4,'Ibuprofen','2026-04-21 11:06:43','2026-04-21 11:06:43'),(5,5,'Codeine','2026-04-21 11:06:45','2026-04-21 11:06:45');
/*!40000 ALTER TABLE `patient_allergies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `age` smallint(6) DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patients_nic_unique` (`nic`),
  UNIQUE KEY `patients_mobile_unique` (`mobile`),
  UNIQUE KEY `patients_phn_unique` (`phn`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,'James Wilson','1966-03-12',NULL,'male','NI660312001','07700900001','PHN001',NULL,NULL,'14 Elm Street, Northfield','2026-04-21 11:06:31','2026-04-21 11:06:31'),(2,'Emma Thompson','1979-07-25',NULL,'female','NI790725002','07700900002','PHN002',NULL,NULL,'7 Birch Avenue, Westbrook','2026-04-21 11:06:36','2026-04-21 11:06:36'),(3,'Robert Davis','1958-11-04',NULL,'male','NI581104003','07700900003','PHN003',NULL,NULL,'32 Oak Lane, Milltown','2026-04-21 11:06:39','2026-04-21 11:06:39'),(4,'Sophie Johnson','1988-02-18',NULL,'female','NI880218004','07700900004','PHN004',NULL,NULL,'5 Maple Close, Eastfield','2026-04-21 11:06:42','2026-04-21 11:06:42'),(5,'George Carter','1952-09-30',NULL,'male','NI520930005','07700900005','PHN005',NULL,NULL,'19 Cedar Road, Southgate','2026-04-21 11:06:45','2026-04-21 11:06:45');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pharmacy_restock_logs`
--

DROP TABLE IF EXISTS `pharmacy_restock_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pharmacy_restock_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unit_view_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned DEFAULT NULL,
  `drug_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` enum('new_stock','restock') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pharmacy_restock_logs_stock_id_foreign` (`stock_id`),
  KEY `pharmacy_restock_logs_performed_by_foreign` (`performed_by`),
  KEY `pharmacy_restock_logs_unit_view_id_created_at_index` (`unit_view_id`,`created_at`),
  CONSTRAINT `pharmacy_restock_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pharmacy_restock_logs_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `pharmacy_stock` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pharmacy_restock_logs_unit_view_id_foreign` FOREIGN KEY (`unit_view_id`) REFERENCES `unit_views` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pharmacy_restock_logs`
--

LOCK TABLES `pharmacy_restock_logs` WRITE;
/*!40000 ALTER TABLE `pharmacy_restock_logs` DISABLE KEYS */;
INSERT INTO `pharmacy_restock_logs` VALUES (1,11,1,'Metformin','new_stock',1000,'2027-06-30','Initial stock entry',38,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(2,11,2,'Amlodipine','new_stock',500,'2027-03-31','Initial stock entry',38,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(3,11,3,'Atorvastatin','new_stock',600,'2027-09-30','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(4,11,4,'Losartan','new_stock',400,'2026-12-31','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(5,11,5,'Omeprazole','new_stock',800,'2027-04-30','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(6,11,6,'Aspirin','new_stock',1000,'2027-06-30','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(7,11,7,'Paracetamol','new_stock',1200,'2027-08-31','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(8,11,8,'Salbutamol','new_stock',50,'2026-11-30','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(9,11,9,'Amoxicillin','new_stock',300,'2026-10-31','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(10,11,10,'Levothyroxine','new_stock',200,'2027-02-28','Initial stock entry',38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(11,11,9,'Amoxicillin','restock',200,'2027-03-31','Emergency restock — stock running low',38,'2026-04-21 11:06:49','2026-04-21 11:06:49');
/*!40000 ALTER TABLE `pharmacy_restock_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pharmacy_stock`
--

DROP TABLE IF EXISTS `pharmacy_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pharmacy_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unit_view_id` bigint(20) unsigned NOT NULL,
  `drug_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `initial_amount` int(10) unsigned NOT NULL DEFAULT 0,
  `remaining` int(10) unsigned NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `is_out_of_stock` tinyint(1) NOT NULL DEFAULT 0,
  `low_stock_threshold` smallint(5) unsigned NOT NULL DEFAULT 10,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pharmacy_stock_created_by_foreign` (`created_by`),
  KEY `pharmacy_stock_updated_by_foreign` (`updated_by`),
  KEY `pharmacy_stock_unit_view_id_drug_name_index` (`unit_view_id`,`drug_name`),
  CONSTRAINT `pharmacy_stock_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pharmacy_stock_unit_view_id_foreign` FOREIGN KEY (`unit_view_id`) REFERENCES `unit_views` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pharmacy_stock_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pharmacy_stock`
--

LOCK TABLES `pharmacy_stock` WRITE;
/*!40000 ALTER TABLE `pharmacy_stock` DISABLE KEYS */;
INSERT INTO `pharmacy_stock` VALUES (1,11,'Metformin',1000,680,'2027-06-30',0,100,NULL,38,38,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(2,11,'Amlodipine',500,312,'2027-03-31',0,50,NULL,38,38,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(3,11,'Atorvastatin',600,445,'2027-09-30',0,60,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(4,11,'Losartan',400,195,'2026-12-31',0,50,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(5,11,'Omeprazole',800,520,'2027-04-30',0,80,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(6,11,'Aspirin',1000,834,'2027-06-30',0,100,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(7,11,'Paracetamol',1200,925,'2027-08-31',0,100,NULL,38,37,'2026-04-21 11:06:49','2026-04-21 11:44:37'),(8,11,'Salbutamol',50,28,'2026-11-30',0,10,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(9,11,'Amoxicillin',500,242,'2026-10-31',0,30,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49'),(10,11,'Levothyroxine',200,148,'2027-02-28',0,20,NULL,38,38,'2026-04-21 11:06:49','2026-04-21 11:06:49');
/*!40000 ALTER TABLE `pharmacy_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescription_dispensings`
--

DROP TABLE IF EXISTS `prescription_dispensings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescription_dispensings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `visit_drug_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('prescribed','os') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prescribed',
  `quantity_dispensed` int(10) unsigned NOT NULL DEFAULT 0,
  `dispensed_by` bigint(20) unsigned DEFAULT NULL,
  `dispensed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prescription_dispensings_visit_drug_id_unique` (`visit_drug_id`),
  KEY `prescription_dispensings_visit_id_foreign` (`visit_id`),
  KEY `prescription_dispensings_stock_id_foreign` (`stock_id`),
  KEY `prescription_dispensings_dispensed_by_foreign` (`dispensed_by`),
  CONSTRAINT `prescription_dispensings_dispensed_by_foreign` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `prescription_dispensings_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `pharmacy_stock` (`id`) ON DELETE SET NULL,
  CONSTRAINT `prescription_dispensings_visit_drug_id_foreign` FOREIGN KEY (`visit_drug_id`) REFERENCES `visit_drugs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prescription_dispensings_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescription_dispensings`
--

LOCK TABLES `prescription_dispensings` WRITE;
/*!40000 ALTER TABLE `prescription_dispensings` DISABLE KEYS */;
INSERT INTO `prescription_dispensings` VALUES (1,51,79,7,'prescribed',15,37,'2026-04-21 11:44:37','2026-04-21 11:44:37','2026-04-21 11:44:37');
/*!40000 ALTER TABLE `prescription_dispensings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terminology_categories`
--

DROP TABLE IF EXISTS `terminology_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terminology_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `terminology_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terminology_categories`
--

LOCK TABLES `terminology_categories` WRITE;
/*!40000 ALTER TABLE `terminology_categories` DISABLE KEYS */;
INSERT INTO `terminology_categories` VALUES (1,'Presenting Complaints','presenting_complaints',NULL,1,0,'2026-04-22 05:07:37','2026-04-22 05:07:37'),(2,'Complaint Durations','complaint_durations',NULL,1,1,'2026-04-22 05:07:37','2026-04-22 05:07:37'),(3,'Past Medical History','past_medical_history',NULL,1,2,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(4,'Past Surgical History','past_surgical_history',NULL,1,3,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(5,'Social History','social_history',NULL,1,4,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(6,'Menstrual History','menstrual_history',NULL,1,5,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(7,'Investigations','investigations',NULL,1,6,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(8,'Diabetes Instructions','diabetes_instructions',NULL,1,7,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(9,'Hypertension Instructions','hypertension_instructions',NULL,1,8,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(10,'Dyslipidemia Instructions','dyslipidemia_instructions',NULL,1,9,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(11,'General Instructions','general_instructions',NULL,1,10,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(12,'Differential Diagnosis','differential_diagnosis',NULL,1,11,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(13,'Working Diagnosis','working_diagnosis',NULL,1,12,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(14,'General Looking','general_looking',NULL,1,13,'2026-04-22 05:07:38','2026-04-22 05:07:38'),(15,'Cardiology Examination Findings','cardiology_findings',NULL,1,14,'2026-04-22 05:07:39','2026-04-22 05:07:39'),(16,'Respiratory Examination Findings','respiratory_findings',NULL,1,15,'2026-04-22 05:07:39','2026-04-22 05:07:39'),(17,'Abdominal Examination Findings','abdominal_findings',NULL,1,16,'2026-04-22 05:07:39','2026-04-22 05:07:39'),(18,'Neurological Examination','neurological_findings',NULL,1,17,'2026-04-22 05:07:39','2026-04-22 05:07:39'),(19,'Dermatological Findings','dermatological_findings',NULL,1,18,'2026-04-22 05:07:39','2026-04-22 05:07:39');
/*!40000 ALTER TABLE `terminology_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terminology_terms`
--

DROP TABLE IF EXISTS `terminology_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terminology_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `terminology_terms_category_term_unique` (`category`,`term`),
  KEY `terminology_terms_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terminology_terms`
--

LOCK TABLES `terminology_terms` WRITE;
/*!40000 ALTER TABLE `terminology_terms` DISABLE KEYS */;
INSERT INTO `terminology_terms` VALUES (1,'presenting_complaints','Headache',NULL,NULL),(2,'presenting_complaints','Chest pain',NULL,NULL),(3,'presenting_complaints','Shortness of breath',NULL,NULL),(4,'presenting_complaints','Fever',NULL,NULL),(5,'presenting_complaints','Cough',NULL,NULL),(6,'presenting_complaints','Abdominal pain',NULL,NULL),(7,'presenting_complaints','Dizziness',NULL,NULL),(8,'presenting_complaints','Weakness',NULL,NULL),(9,'presenting_complaints','Fatigue',NULL,NULL),(10,'presenting_complaints','Back pain',NULL,NULL),(11,'presenting_complaints','Joint pain',NULL,NULL),(12,'presenting_complaints','Leg swelling',NULL,NULL),(13,'presenting_complaints','Palpitations',NULL,NULL),(14,'presenting_complaints','Nausea',NULL,NULL),(15,'presenting_complaints','Vomiting',NULL,NULL),(16,'presenting_complaints','Diarrhoea',NULL,NULL),(17,'presenting_complaints','Constipation',NULL,NULL),(18,'presenting_complaints','Dysuria',NULL,NULL),(19,'presenting_complaints','Haematuria',NULL,NULL),(20,'presenting_complaints','Weight loss',NULL,NULL),(21,'presenting_complaints','Weight gain',NULL,NULL),(22,'presenting_complaints','Loss of appetite',NULL,NULL),(23,'presenting_complaints','Blurred vision',NULL,NULL),(24,'presenting_complaints','Difficulty swallowing',NULL,NULL),(25,'presenting_complaints','Skin rash',NULL,NULL),(26,'presenting_complaints','Numbness',NULL,NULL),(27,'presenting_complaints','Tingling',NULL,NULL),(28,'presenting_complaints','Syncope',NULL,NULL),(29,'presenting_complaints','Polyuria',NULL,NULL),(30,'presenting_complaints','Polydipsia',NULL,NULL),(31,'presenting_complaints','Ear pain',NULL,NULL),(32,'presenting_complaints','Nasal congestion',NULL,NULL),(33,'presenting_complaints','Tremor',NULL,NULL),(34,'presenting_complaints','Insomnia',NULL,NULL),(35,'presenting_complaints','Toothache',NULL,NULL),(36,'complaint_durations','1 day',NULL,NULL),(37,'complaint_durations','2 days',NULL,NULL),(38,'complaint_durations','3 days',NULL,NULL),(39,'complaint_durations','4 days',NULL,NULL),(40,'complaint_durations','5 days',NULL,NULL),(41,'complaint_durations','6 days',NULL,NULL),(42,'complaint_durations','1 week',NULL,NULL),(43,'complaint_durations','2 weeks',NULL,NULL),(44,'complaint_durations','3 weeks',NULL,NULL),(45,'complaint_durations','1 month',NULL,NULL),(46,'complaint_durations','2 months',NULL,NULL),(47,'complaint_durations','3 months',NULL,NULL),(48,'complaint_durations','6 months',NULL,NULL),(49,'complaint_durations','1 year',NULL,NULL),(50,'complaint_durations','2 years',NULL,NULL),(51,'complaint_durations','More than 2 years',NULL,NULL),(52,'complaint_durations','Since childhood',NULL,NULL),(53,'complaint_durations','Worsening over time',NULL,NULL),(54,'complaint_durations','Intermittent',NULL,NULL),(55,'past_medical_history','Diabetes Mellitus Type 2',NULL,NULL),(56,'past_medical_history','Hypertension',NULL,NULL),(57,'past_medical_history','Ischemic Heart Disease',NULL,NULL),(58,'past_medical_history','Dyslipidaemia',NULL,NULL),(59,'past_medical_history','Asthma',NULL,NULL),(60,'past_medical_history','COPD',NULL,NULL),(61,'past_medical_history','Chronic Kidney Disease',NULL,NULL),(62,'past_medical_history','Hypothyroidism',NULL,NULL),(63,'past_medical_history','Hyperthyroidism',NULL,NULL),(64,'past_medical_history','Epilepsy',NULL,NULL),(65,'past_medical_history','Stroke',NULL,NULL),(66,'past_medical_history','Atrial Fibrillation',NULL,NULL),(67,'past_medical_history','Heart Failure',NULL,NULL),(68,'past_medical_history','Rheumatoid Arthritis',NULL,NULL),(69,'past_medical_history','Osteoporosis',NULL,NULL),(70,'past_medical_history','Gout',NULL,NULL),(71,'past_medical_history','Peptic Ulcer Disease',NULL,NULL),(72,'past_medical_history','GORD',NULL,NULL),(73,'past_medical_history','Liver Cirrhosis',NULL,NULL),(74,'past_medical_history','Chronic Hepatitis B',NULL,NULL),(75,'past_medical_history','Chronic Hepatitis C',NULL,NULL),(76,'past_medical_history','Tuberculosis',NULL,NULL),(77,'past_medical_history','Depression',NULL,NULL),(78,'past_medical_history','Anxiety',NULL,NULL),(79,'past_medical_history','Anaemia',NULL,NULL),(80,'past_medical_history','Thalassaemia',NULL,NULL),(81,'past_medical_history','Dengue',NULL,NULL),(82,'past_medical_history','Malaria',NULL,NULL),(83,'past_medical_history','Cancer (specify)',NULL,NULL),(84,'past_medical_history','Parkinson\'s Disease',NULL,NULL),(85,'past_medical_history','Bipolar Disorder',NULL,NULL),(86,'past_surgical_history','Appendicectomy',NULL,NULL),(87,'past_surgical_history','Cholecystectomy',NULL,NULL),(88,'past_surgical_history','Hernia Repair',NULL,NULL),(89,'past_surgical_history','CABG',NULL,NULL),(90,'past_surgical_history','Valve Replacement',NULL,NULL),(91,'past_surgical_history','Hip Replacement',NULL,NULL),(92,'past_surgical_history','Knee Replacement',NULL,NULL),(93,'past_surgical_history','Hysterectomy',NULL,NULL),(94,'past_surgical_history','Caesarean Section',NULL,NULL),(95,'past_surgical_history','Tonsillectomy',NULL,NULL),(96,'past_surgical_history','Thyroidectomy',NULL,NULL),(97,'past_surgical_history','Mastectomy',NULL,NULL),(98,'past_surgical_history','Prostatectomy',NULL,NULL),(99,'past_surgical_history','Splenectomy',NULL,NULL),(100,'past_surgical_history','Colostomy',NULL,NULL),(101,'past_surgical_history','Cataract Surgery',NULL,NULL),(102,'past_surgical_history','PTCA with Stenting',NULL,NULL),(103,'past_surgical_history','Laparotomy',NULL,NULL),(104,'past_surgical_history','Dental Extraction',NULL,NULL),(105,'social_history','Non-smoker',NULL,NULL),(106,'social_history','Current smoker',NULL,NULL),(107,'social_history','Ex-smoker',NULL,NULL),(108,'social_history','Social alcohol use',NULL,NULL),(109,'social_history','Regular alcohol use',NULL,NULL),(110,'social_history','Non-alcoholic',NULL,NULL),(111,'social_history','Betel chewing',NULL,NULL),(112,'social_history','Independent ADLs',NULL,NULL),(113,'social_history','Dependent for ADLs',NULL,NULL),(114,'social_history','Lives alone',NULL,NULL),(115,'social_history','Lives with family',NULL,NULL),(116,'social_history','Farmer',NULL,NULL),(117,'social_history','Office worker',NULL,NULL),(118,'social_history','Manual labourer',NULL,NULL),(119,'social_history','Retired',NULL,NULL),(120,'menstrual_history','Regular cycles',NULL,NULL),(121,'menstrual_history','Irregular cycles',NULL,NULL),(122,'menstrual_history','Oligomenorrhoea',NULL,NULL),(123,'menstrual_history','Amenorrhoea',NULL,NULL),(124,'menstrual_history','Dysmenorrhoea',NULL,NULL),(125,'menstrual_history','Menorrhagia',NULL,NULL),(126,'menstrual_history','Post-menopausal',NULL,NULL),(127,'menstrual_history','Pre-menopausal',NULL,NULL),(128,'menstrual_history','LMP normal',NULL,NULL),(129,'menstrual_history','Peri-menopausal',NULL,NULL),(130,'investigations','Full Blood Count',NULL,NULL),(131,'investigations','Serum Creatinine',NULL,NULL),(132,'investigations','eGFR',NULL,NULL),(133,'investigations','Blood Urea',NULL,NULL),(134,'investigations','Serum Electrolytes',NULL,NULL),(135,'investigations','Fasting Blood Sugar',NULL,NULL),(136,'investigations','HbA1c',NULL,NULL),(137,'investigations','Lipid Profile',NULL,NULL),(138,'investigations','Liver Function Tests',NULL,NULL),(139,'investigations','TSH',NULL,NULL),(140,'investigations','FT4',NULL,NULL),(141,'investigations','ECG',NULL,NULL),(142,'investigations','Chest X-ray',NULL,NULL),(143,'investigations','Echocardiogram',NULL,NULL),(144,'investigations','Urine Full Report',NULL,NULL),(145,'investigations','Urine Culture',NULL,NULL),(146,'investigations','Urine Protein/Creatinine Ratio',NULL,NULL),(147,'investigations','Stool Full Report',NULL,NULL),(148,'investigations','Peripheral Blood Film',NULL,NULL),(149,'investigations','Serum Uric Acid',NULL,NULL),(150,'investigations','Serum Calcium',NULL,NULL),(151,'investigations','Serum Phosphate',NULL,NULL),(152,'investigations','Serum Albumin',NULL,NULL),(153,'investigations','CRP',NULL,NULL),(154,'investigations','ESR',NULL,NULL),(155,'investigations','Prothrombin Time / INR',NULL,NULL),(156,'investigations','Blood Culture',NULL,NULL),(157,'investigations','D-dimer',NULL,NULL),(158,'investigations','HBsAg',NULL,NULL),(159,'investigations','Anti-HCV',NULL,NULL),(160,'investigations','Serum Vitamin B12',NULL,NULL),(161,'investigations','Serum Folate',NULL,NULL),(162,'investigations','Renal Ultrasound',NULL,NULL),(163,'investigations','Abdominal Ultrasound',NULL,NULL),(164,'investigations','CT Abdomen',NULL,NULL),(165,'investigations','MRI Brain',NULL,NULL),(166,'investigations','Fasting Lipid Profile',NULL,NULL),(167,'investigations','Random Blood Sugar',NULL,NULL),(168,'diabetes_instructions','Follow diabetic diet — avoid simple sugars',NULL,NULL),(169,'diabetes_instructions','Monitor blood glucose regularly',NULL,NULL),(170,'diabetes_instructions','Check fasting blood glucose daily',NULL,NULL),(171,'diabetes_instructions','HbA1c test every 3 months',NULL,NULL),(172,'diabetes_instructions','Annual eye review',NULL,NULL),(173,'diabetes_instructions','Annual foot examination',NULL,NULL),(174,'diabetes_instructions','Carry glucose tablets at all times',NULL,NULL),(175,'diabetes_instructions','Avoid hypoglycaemic episodes',NULL,NULL),(176,'diabetes_instructions','Exercise 30 minutes daily',NULL,NULL),(177,'diabetes_instructions','Regular follow-up required',NULL,NULL),(178,'diabetes_instructions','Annual creatinine and urine microalbumin',NULL,NULL),(179,'diabetes_instructions','Sick day rules explained',NULL,NULL),(180,'diabetes_instructions','Diabetic foot care education given',NULL,NULL),(181,'hypertension_instructions','Low salt diet — limit sodium to < 2 g per day',NULL,NULL),(182,'hypertension_instructions','Monitor blood pressure at home regularly',NULL,NULL),(183,'hypertension_instructions','Avoid NSAIDs and COX-2 inhibitors',NULL,NULL),(184,'hypertension_instructions','Reduce weight if overweight',NULL,NULL),(185,'hypertension_instructions','Exercise 30–45 minutes most days',NULL,NULL),(186,'hypertension_instructions','Limit alcohol intake',NULL,NULL),(187,'hypertension_instructions','Stop smoking',NULL,NULL),(188,'hypertension_instructions','Medication adherence important',NULL,NULL),(189,'hypertension_instructions','Regular follow-up required',NULL,NULL),(190,'hypertension_instructions','Home BP log to be maintained',NULL,NULL),(191,'dyslipidaemia_instructions','Low saturated fat diet',NULL,NULL),(192,'dyslipidaemia_instructions','Avoid trans fats',NULL,NULL),(193,'dyslipidaemia_instructions','Increase dietary fibre',NULL,NULL),(194,'dyslipidaemia_instructions','Regular aerobic exercise',NULL,NULL),(195,'dyslipidaemia_instructions','Annual lipid profile',NULL,NULL),(196,'dyslipidaemia_instructions','Mediterranean diet advised',NULL,NULL),(197,'dyslipidaemia_instructions','Avoid processed foods',NULL,NULL),(198,'dyslipidaemia_instructions','Limit red meat consumption',NULL,NULL),(199,'general_instructions','Review in 1 week',NULL,NULL),(200,'general_instructions','Review in 2 weeks',NULL,NULL),(201,'general_instructions','Review in 1 month',NULL,NULL),(202,'general_instructions','Review in 3 months',NULL,NULL),(203,'general_instructions','Review in 6 months',NULL,NULL),(204,'general_instructions','Come back if symptoms worsen',NULL,NULL),(205,'general_instructions','Seek urgent care if chest pain',NULL,NULL),(206,'general_instructions','Medication compliance is important',NULL,NULL),(207,'general_instructions','Take medications with meals',NULL,NULL),(208,'general_instructions','Take medications before meals',NULL,NULL),(209,'general_instructions','Do not stop medications without advice',NULL,NULL),(210,'general_instructions','Reduce weight',NULL,NULL),(211,'general_instructions','Increase physical activity',NULL,NULL),(212,'general_instructions','Stay well hydrated',NULL,NULL),(213,'general_instructions','Rest and adequate sleep',NULL,NULL),(214,'general_instructions','Smoking cessation strongly advised',NULL,NULL),(215,'general_instructions','Return immediately if breathless or severe pain',NULL,NULL),(216,'differential_diagnosis','Diabetes Mellitus Type 2',NULL,NULL),(217,'differential_diagnosis','Hypertension',NULL,NULL),(218,'differential_diagnosis','Ischemic Heart Disease',NULL,NULL),(219,'differential_diagnosis','Heart Failure',NULL,NULL),(220,'differential_diagnosis','COPD',NULL,NULL),(221,'differential_diagnosis','Asthma',NULL,NULL),(222,'differential_diagnosis','Pneumonia',NULL,NULL),(223,'differential_diagnosis','Pulmonary Embolism',NULL,NULL),(224,'differential_diagnosis','Pleural Effusion',NULL,NULL),(225,'differential_diagnosis','Acute Coronary Syndrome',NULL,NULL),(226,'differential_diagnosis','Stroke',NULL,NULL),(227,'differential_diagnosis','TIA',NULL,NULL),(228,'differential_diagnosis','Urinary Tract Infection',NULL,NULL),(229,'differential_diagnosis','Pyelonephritis',NULL,NULL),(230,'differential_diagnosis','Chronic Kidney Disease',NULL,NULL),(231,'differential_diagnosis','Nephrotic Syndrome',NULL,NULL),(232,'differential_diagnosis','Peptic Ulcer',NULL,NULL),(233,'differential_diagnosis','GORD',NULL,NULL),(234,'differential_diagnosis','Acute Pancreatitis',NULL,NULL),(235,'differential_diagnosis','Hepatitis',NULL,NULL),(236,'differential_diagnosis','Liver Cirrhosis',NULL,NULL),(237,'differential_diagnosis','Anaemia',NULL,NULL),(238,'differential_diagnosis','Thyroid Disease',NULL,NULL),(239,'differential_diagnosis','Gout',NULL,NULL),(240,'differential_diagnosis','Rheumatoid Arthritis',NULL,NULL),(241,'differential_diagnosis','SLE',NULL,NULL),(242,'differential_diagnosis','Epilepsy',NULL,NULL),(243,'differential_diagnosis','Migraine',NULL,NULL),(244,'differential_diagnosis','Depression',NULL,NULL),(245,'differential_diagnosis','Anxiety',NULL,NULL),(246,'differential_diagnosis','Cellulitis',NULL,NULL),(247,'differential_diagnosis','DVT',NULL,NULL),(248,'differential_diagnosis','Dental Caries',NULL,NULL),(249,'differential_diagnosis','Periodontitis',NULL,NULL),(250,'working_diagnosis','Diabetes Mellitus Type 2 – Poor Glycaemic Control',NULL,NULL),(251,'working_diagnosis','Diabetes Mellitus Type 2 – Well Controlled',NULL,NULL),(252,'working_diagnosis','Hypertension – Uncontrolled',NULL,NULL),(253,'working_diagnosis','Hypertension – Controlled',NULL,NULL),(254,'working_diagnosis','Ischemic Heart Disease – Stable',NULL,NULL),(255,'working_diagnosis','Heart Failure – NYHA Class II',NULL,NULL),(256,'working_diagnosis','COPD – Stable',NULL,NULL),(257,'working_diagnosis','Asthma – Moderate Persistent',NULL,NULL),(258,'working_diagnosis','Community Acquired Pneumonia',NULL,NULL),(259,'working_diagnosis','Urinary Tract Infection – Lower',NULL,NULL),(260,'working_diagnosis','Pyelonephritis',NULL,NULL),(261,'working_diagnosis','Chronic Kidney Disease – Stage 3',NULL,NULL),(262,'working_diagnosis','Gout – Acute Attack',NULL,NULL),(263,'working_diagnosis','Anaemia – Iron Deficiency',NULL,NULL),(264,'working_diagnosis','Hypothyroidism',NULL,NULL),(265,'working_diagnosis','Dyslipidaemia',NULL,NULL),(266,'working_diagnosis','Dental Caries',NULL,NULL),(267,'working_diagnosis','Acute Periodontitis',NULL,NULL),(268,'general_looking','Well-looking',NULL,NULL),(269,'general_looking','Well-nourished',NULL,NULL),(270,'general_looking','Ill-looking',NULL,NULL),(271,'general_looking','Mildly unwell',NULL,NULL),(272,'general_looking','Moderately unwell',NULL,NULL),(273,'general_looking','Severely unwell',NULL,NULL),(274,'general_looking','Pale',NULL,NULL),(275,'general_looking','Icteric',NULL,NULL),(276,'general_looking','Cyanosed',NULL,NULL),(277,'general_looking','Oedematous',NULL,NULL),(278,'general_looking','Dehydrated',NULL,NULL),(279,'general_looking','Febrile',NULL,NULL),(280,'general_looking','Afebrile',NULL,NULL),(281,'general_looking','Alert and conscious',NULL,NULL),(282,'general_looking','Confused',NULL,NULL),(283,'general_looking','In no distress',NULL,NULL),(284,'general_looking','In mild distress',NULL,NULL),(285,'general_looking','In moderate distress',NULL,NULL),(286,'cardiology_findings','Normal S1 S2 heard',NULL,NULL),(287,'cardiology_findings','No murmurs',NULL,NULL),(288,'cardiology_findings','Regular rhythm',NULL,NULL),(289,'cardiology_findings','Irregular rhythm',NULL,NULL),(290,'cardiology_findings','Apex beat not displaced',NULL,NULL),(291,'cardiology_findings','Apex beat displaced laterally',NULL,NULL),(292,'cardiology_findings','No raised JVP',NULL,NULL),(293,'cardiology_findings','Raised JVP',NULL,NULL),(294,'cardiology_findings','Muffled heart sounds',NULL,NULL),(295,'cardiology_findings','Systolic murmur grade II',NULL,NULL),(296,'cardiology_findings','Systolic murmur grade III',NULL,NULL),(297,'cardiology_findings','Pansystolic murmur at apex',NULL,NULL),(298,'cardiology_findings','Ejection systolic murmur',NULL,NULL),(299,'respiratory_findings','Clear air entry bilaterally',NULL,NULL),(300,'respiratory_findings','Reduced air entry left base',NULL,NULL),(301,'respiratory_findings','Reduced air entry right base',NULL,NULL),(302,'respiratory_findings','Bilateral basal crepitations',NULL,NULL),(303,'respiratory_findings','Bilateral wheeze',NULL,NULL),(304,'respiratory_findings','Dullness to percussion at left base',NULL,NULL),(305,'respiratory_findings','Dullness to percussion at right base',NULL,NULL),(306,'respiratory_findings','No added sounds',NULL,NULL),(307,'respiratory_findings','Tachypnoeic at rest',NULL,NULL),(308,'respiratory_findings','Good air entry bilaterally',NULL,NULL),(309,'respiratory_findings','Rhonchi bilaterally',NULL,NULL),(310,'abdominal_findings','Soft and non-tender',NULL,NULL),(311,'abdominal_findings','Mild epigastric tenderness',NULL,NULL),(312,'abdominal_findings','Right iliac fossa tenderness',NULL,NULL),(313,'abdominal_findings','Hepatomegaly – 2 cm below costal margin',NULL,NULL),(314,'abdominal_findings','Splenomegaly – 3 cm below costal margin',NULL,NULL),(315,'abdominal_findings','No organomegaly',NULL,NULL),(316,'abdominal_findings','Ascites present',NULL,NULL),(317,'abdominal_findings','No ascites',NULL,NULL),(318,'abdominal_findings','Renal angle tenderness on right',NULL,NULL),(319,'abdominal_findings','Bowel sounds normal',NULL,NULL),(320,'abdominal_findings','Distended abdomen',NULL,NULL),(321,'abdominal_findings','Guarding present',NULL,NULL),(322,'neurological_findings','Alert and oriented',NULL,NULL),(323,'neurological_findings','GCS 15/15',NULL,NULL),(324,'neurological_findings','Normal power all four limbs',NULL,NULL),(325,'neurological_findings','Normal tone',NULL,NULL),(326,'neurological_findings','Normal reflexes',NULL,NULL),(327,'neurological_findings','No focal neurological deficits',NULL,NULL),(328,'neurological_findings','Confused – GCS 14/15',NULL,NULL),(329,'neurological_findings','Mild weakness right upper limb',NULL,NULL),(330,'neurological_findings','Ataxic gait',NULL,NULL),(331,'neurological_findings','Tremor present',NULL,NULL),(332,'neurological_findings','Sensory loss in feet bilaterally',NULL,NULL),(333,'dermatological_findings','No rash',NULL,NULL),(334,'dermatological_findings','No skin lesions',NULL,NULL),(335,'dermatological_findings','Dry skin',NULL,NULL),(336,'dermatological_findings','Oedema of feet',NULL,NULL),(337,'dermatological_findings','Pitting oedema bilateral ankles',NULL,NULL),(338,'dermatological_findings','Erythema present',NULL,NULL),(339,'dermatological_findings','Petechiae noted',NULL,NULL),(340,'dermatological_findings','Cellulitis right leg',NULL,NULL),(341,'dermatological_findings','Normal skin',NULL,NULL),(342,'dermatological_findings','Pallor of palmar creases',NULL,NULL),(343,'dermatological_findings','Jaundice noted',NULL,NULL),(344,'dermatological_findings','Gum inflammation',NULL,NULL);
/*!40000 ALTER TABLE `terminology_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_templates`
--

DROP TABLE IF EXISTS `unit_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_templates_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_templates`
--

LOCK TABLES `unit_templates` WRITE;
/*!40000 ALTER TABLE `unit_templates` DISABLE KEYS */;
INSERT INTO `unit_templates` VALUES (1,'General Medical Clinic','GMC',1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(2,'Dental Clinic','DC',1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(3,'General Inward','GI',1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(4,'General Pharmacy','GP',1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(5,'Office','OFFICE',1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(7,'Nephrology Clinic','NEC',0,'2026-04-22 01:24:36','2026-04-22 01:24:36');
/*!40000 ALTER TABLE `unit_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_views`
--

DROP TABLE IF EXISTS `unit_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `view_template_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unit_views_unit_id_foreign` (`unit_id`),
  KEY `unit_views_view_template_id_foreign` (`view_template_id`),
  CONSTRAINT `unit_views_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `unit_views_view_template_id_foreign` FOREIGN KEY (`view_template_id`) REFERENCES `view_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_views`
--

LOCK TABLES `unit_views` WRITE;
/*!40000 ALTER TABLE `unit_views` DISABLE KEYS */;
INSERT INTO `unit_views` VALUES (1,'GMC Ward A – Doctor',1,1,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(2,'GMC Ward A – Clerk',1,2,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(3,'GMC Ward A – Nurse',1,3,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(4,'Dental Clinic – Doctor',2,4,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(5,'Dental Clinic – Clerk',2,5,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(6,'Dental Clinic – Nurse',2,6,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(7,'General Inward – Doctor',3,7,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(8,'General Inward – Clerk',3,8,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(9,'General Inward – Nurse',3,9,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(10,'GP OPD – Doctor',4,10,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(11,'GP OPD – Pharmacist',4,11,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(12,'GP OPD – Clerk',4,12,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(13,'General Medical Clinic – Doctor',5,1,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(14,'General Medical Clinic – Clerk',5,2,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(15,'General Medical Clinic – Nurse',5,3,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(16,'General Pharmacy OPD – Pharmacist',6,11,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(17,'General Pharmacy OPD – Clerk',6,12,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(18,'General Medicine Ward – Doctor',7,1,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(19,'General Medicine Ward – Clerk',7,2,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(20,'General Medicine Ward – Nurse',7,3,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(21,'General Practice – Pharmacist',8,11,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(22,'General Practice – Clerk',8,12,'2026-04-21 11:06:20','2026-04-21 11:06:20'),(24,'NEC Doctor View',11,17,'2026-04-22 01:26:13','2026-04-22 01:26:13');
/*!40000 ALTER TABLE `unit_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue_started_at` datetime DEFAULT NULL,
  `current_queue_session` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `institution_id` bigint(20) unsigned NOT NULL,
  `unit_template_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_institution_id_foreign` (`institution_id`),
  KEY `units_unit_template_id_foreign` (`unit_template_id`),
  CONSTRAINT `units_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `units_unit_template_id_foreign` FOREIGN KEY (`unit_template_id`) REFERENCES `unit_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'GMC Ward A','1',NULL,1,3,1,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(2,'Dental Clinic','2',NULL,1,3,2,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(3,'General Inward Ward B','3',NULL,1,3,3,'2026-04-21 11:06:18','2026-04-21 11:06:18'),(4,'General Practice OPD','4',NULL,1,3,4,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(5,'General Medical Clinic','1',NULL,1,4,1,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(6,'General Pharmacy OPD','2',NULL,1,4,4,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(7,'General Medicine Ward','1',NULL,1,6,1,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(8,'General Practice','2',NULL,1,6,4,'2026-04-21 11:06:19','2026-04-21 11:06:19'),(11,'Nephrology Clinic','5',NULL,1,3,7,'2026-04-22 01:25:39','2026-04-22 01:25:39');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_units`
--

DROP TABLE IF EXISTS `user_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_units` (
  `user_id` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`unit_id`),
  KEY `user_units_unit_id_foreign` (`unit_id`),
  CONSTRAINT `user_units_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_units_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_units`
--

LOCK TABLES `user_units` WRITE;
/*!40000 ALTER TABLE `user_units` DISABLE KEYS */;
INSERT INTO `user_units` VALUES (31,1),(32,1),(33,1),(34,2),(35,2),(36,2),(37,1),(37,4),(37,11),(38,4),(39,4),(40,5),(41,5),(42,7),(43,7);
/*!40000 ALTER TABLE `user_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_views`
--

DROP TABLE IF EXISTS `user_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_views` (
  `user_id` bigint(20) unsigned NOT NULL,
  `unit_view_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`unit_view_id`),
  KEY `user_views_unit_view_id_foreign` (`unit_view_id`),
  CONSTRAINT `user_views_unit_view_id_foreign` FOREIGN KEY (`unit_view_id`) REFERENCES `unit_views` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_views_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_views`
--

LOCK TABLES `user_views` WRITE;
/*!40000 ALTER TABLE `user_views` DISABLE KEYS */;
INSERT INTO `user_views` VALUES (31,1),(32,2),(33,3),(34,4),(35,5),(36,6),(37,1),(37,2),(37,3),(37,10),(37,11),(37,12),(37,24),(38,11),(39,12),(40,13),(41,15),(42,18),(43,20);
/*!40000 ALTER TABLE `user_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialty` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `institution_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_institution_id_foreign` (`institution_id`),
  CONSTRAINT `users_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@phims.lk',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'$2y$12$Jvr9o2Sum6.0Q0uWQQUZyeBMQKG0iHuZmCrYzHo59i4cUxcMkCLuq',NULL,'2026-02-23 20:46:04','2026-02-23 20:46:04',NULL),(31,'Dr. Sarah Mitchell','sarah.mitchell@stgeorges.nhs',NULL,NULL,'female',NULL,'Senior Registrar','General Medicine','MBChB, MD (Medicine)','GMC-112345',NULL,NULL,'user',NULL,'$2y$12$/uq2sfGfruSQ7FKAKuqsj.Vyh6EU0C5jBVlDdIqf2.rULLkQRwrAq',NULL,'2026-04-21 11:06:21','2026-04-21 11:06:21',3),(32,'Mr. James Harrison','james.harrison@stgeorges.nhs',NULL,NULL,'male',NULL,'Medical Records Officer',NULL,NULL,NULL,NULL,NULL,'user',NULL,'$2y$12$ORryZghOOh.WFHQCGMtyhOIKOkwN8v/bxeU2069NwYGHOHTKmjbEW',NULL,'2026-04-21 11:06:21','2026-04-21 11:06:21',3),(33,'Nurse Emily Clarke','emily.clarke@stgeorges.nhs',NULL,NULL,'female',NULL,'Staff Nurse',NULL,'BSc Nursing',NULL,NULL,NULL,'user',NULL,'$2y$12$PbO8e9JNlSrsM9MdZltKrutBjeCltl.774LtKzZy1lULgN1/g2aZS',NULL,'2026-04-21 11:06:22','2026-04-21 11:06:22',3),(34,'Dr. Robert Anderson','robert.anderson@stgeorges.nhs',NULL,NULL,'male',NULL,'Dental Surgeon','Dentistry','BDS, MDS','GDC-223456',NULL,NULL,'user',NULL,'$2y$12$Wm.t23a5JODMV0DnQ0iKYecs14UjwXKLX6fqHv7oiN0tMYerQXz1S',NULL,'2026-04-21 11:06:22','2026-04-21 11:06:22',3),(35,'Ms. Laura Thompson','laura.thompson@stgeorges.nhs',NULL,NULL,'female',NULL,'Clinic Clerk',NULL,NULL,NULL,NULL,NULL,'user',NULL,'$2y$12$diDwlnxDeoIkaAFibK9Lcusaj0a2xsP9uu5xkrFM442J3msq53e72',NULL,'2026-04-21 11:06:23','2026-04-21 11:06:23',3),(36,'Nurse Patricia White','patricia.white@stgeorges.nhs',NULL,NULL,'female',NULL,'Staff Nurse',NULL,'BSc Nursing',NULL,NULL,NULL,'user',NULL,'$2y$12$Q9/9aD3AlG/nr6.0H6mKFe.fzIckUjSleI09IgSXEBZtWfQjEsH8m',NULL,'2026-04-21 11:06:23','2026-04-21 11:06:23',3),(37,'Dr. Michael Roberts','michael.roberts@stgeorges.nhs',NULL,NULL,'male',NULL,'General Practitioner','General Practice','MBChB, MRCGP','GMC-334567',NULL,NULL,'user',NULL,'$2y$12$XxpgzpSP1ZH4hVYduySNrO7wtobd51.bcN1/ijqCNq6yfHVYGjElW',NULL,'2026-04-21 11:06:23','2026-04-22 01:26:44',3),(38,'Mr. David Collins','david.collins@stgeorges.nhs',NULL,NULL,'male',NULL,'Pharmacist',NULL,'MPharm',NULL,NULL,NULL,'user',NULL,'$2y$12$l5eMITJzo77wcOfOlatlG.Z4CNI6e3FeBLDDtuNeNaWv95lQ4VUnK',NULL,'2026-04-21 11:06:24','2026-04-21 11:06:24',3),(39,'Ms. Jennifer Baker','jennifer.baker@stgeorges.nhs',NULL,NULL,'female',NULL,'OPD Clerk',NULL,NULL,NULL,NULL,NULL,'user',NULL,'$2y$12$lVZh/Vh5H8UZf96t3m897u3QGp5DXLd3xcs7HM27L5PtQaRUCm4DC',NULL,'2026-04-21 11:06:24','2026-04-21 11:06:24',3),(40,'Dr. William Turner','william.turner@riverside.nhs',NULL,NULL,'male',NULL,'Medical Officer','General Medicine','MBChB','GMC-445678',NULL,NULL,'user',NULL,'$2y$12$6vGRWrN6ZDdSlBQZn3asee//ESBNAqxRHrmlGQgEKSMj25FPdBeoC',NULL,'2026-04-21 11:06:24','2026-04-21 11:06:24',4),(41,'Nurse Catherine Evans','catherine.evans@riverside.nhs',NULL,NULL,'female',NULL,'Staff Nurse',NULL,'BSc Nursing',NULL,NULL,NULL,'user',NULL,'$2y$12$k7eB4xCYm.Y2tniiQx524eRVOzl7sf8bGeM3i/eW/kzVFlC.Hr2E2',NULL,'2026-04-21 11:06:25','2026-04-21 11:06:25',4),(42,'Dr. Thomas Hughes','thomas.hughes@westfield.nhs',NULL,NULL,'male',NULL,'Registrar','General Medicine','MBChB, MD','GMC-556789',NULL,NULL,'user',NULL,'$2y$12$lKJSMHj.JTrz./1tMu4bSuP30io2GyDWB/QJjtxiW93OdMmvAvymK',NULL,'2026-04-21 11:06:25','2026-04-21 11:06:25',6),(43,'Nurse Margaret Hall','margaret.hall@westfield.nhs',NULL,NULL,'female',NULL,'Staff Nurse',NULL,'BSc Nursing',NULL,NULL,NULL,'user',NULL,'$2y$12$nARnISYVLMHJorXPr/KPuuz4bygzvicjDXdJ3eBn6sdYulS/yHH2W',NULL,'2026-04-21 11:06:26','2026-04-21 11:06:26',6);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `view_templates`
--

DROP TABLE IF EXISTS `view_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `view_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blade_path` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `unit_template_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `view_templates_code_unique` (`code`),
  KEY `view_templates_unit_template_id_foreign` (`unit_template_id`),
  CONSTRAINT `view_templates_unit_template_id_foreign` FOREIGN KEY (`unit_template_id`) REFERENCES `unit_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `view_templates`
--

LOCK TABLES `view_templates` WRITE;
/*!40000 ALTER TABLE `view_templates` DISABLE KEYS */;
INSERT INTO `view_templates` VALUES (1,'GMC - Doctor View','gmc-doctor','clinical.gmc.doctor',1,1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(2,'GMC - Clerk View','gmc-clerk','clinical.gmc.clerk',1,1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(3,'GMC - Nurse View','gmc-nurse','clinical.gmc.nurse',1,1,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(4,'DC - Doctor View','dc-doctor','clinical.dc.doctor',1,2,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(5,'DC - Clerk View','dc-clerk','clinical.dc.clerk',1,2,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(6,'DC - Nurse View','dc-nurse','clinical.dc.nurse',1,2,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(7,'GI - Doctor View','gi-doctor','clinical.gi.doctor',1,3,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(8,'GI - Clerk View','gi-clerk','clinical.gi.clerk',1,3,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(9,'GI - Nurse View','gi-nurse','clinical.gi.nurse',1,3,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(10,'GP - Doctor View','gp-doctor','clinical.gp.doctor',1,4,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(11,'GP - Pharmacist View','gp-pharmacist','clinical.gp.pharmacist',1,4,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(12,'GP - Clerk View','gp-clerk','clinical.gp.clerk',1,4,'2026-02-23 20:46:02','2026-04-22 00:44:55'),(13,'Office - Doctor View','office-doctor','clinical.office.doctor',1,5,'2026-02-23 20:46:03','2026-04-22 00:44:55'),(14,'Office - Nurse View','office-nurse','clinical.office.nurse',1,5,'2026-02-23 20:46:03','2026-04-22 00:44:55'),(15,'Office - Clerk View','office-clerk','clinical.office.clerk',1,5,'2026-02-23 20:46:03','2026-04-22 00:44:55'),(17,'NEC Doctor','nec-doctor','clinical.nec.doctor',0,7,'2026-04-22 01:25:07','2026-04-22 01:25:07');
/*!40000 ALTER TABLE `view_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_drug_changes`
--

DROP TABLE IF EXISTS `visit_drug_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_drug_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `drug_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` enum('added','edited','deleted') COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_drug_changes_visit_id_foreign` (`visit_id`),
  KEY `visit_drug_changes_user_id_foreign` (`user_id`),
  CONSTRAINT `visit_drug_changes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visit_drug_changes_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_drug_changes`
--

LOCK TABLES `visit_drug_changes` WRITE;
/*!40000 ALTER TABLE `visit_drug_changes` DISABLE KEYS */;
INSERT INTO `visit_drug_changes` VALUES (1,51,79,37,'added',NULL,'{\"section\":\"management\",\"type\":\"Oral\",\"name\":\"Paracetamol\",\"dose\":\"500\",\"unit\":\"mg\",\"frequency\":\"tds\",\"duration\":\"5 days\"}','2026-04-21 11:42:32','2026-04-21 11:42:32');
/*!40000 ALTER TABLE `visit_drug_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_drugs`
--

DROP TABLE IF EXISTS `visit_drugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_drugs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `section` enum('clinic','management') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'clinic',
  `type` enum('Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Oral',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dose` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` enum('mg','g','mcg','ml','tabs','item') COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` enum('mane','nocte','bd','tds','daily','EOD','SOS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_drugs_visit_id_foreign` (`visit_id`),
  KEY `visit_drugs_created_by_foreign` (`created_by`),
  KEY `visit_drugs_updated_by_foreign` (`updated_by`),
  CONSTRAINT `visit_drugs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visit_drugs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `visit_drugs_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_drugs`
--

LOCK TABLES `visit_drugs` WRITE;
/*!40000 ALTER TABLE `visit_drugs` DISABLE KEYS */;
INSERT INTO `visit_drugs` VALUES (1,2,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(2,2,'clinic','Oral','Amlodipine','5','mg','mane','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(3,2,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(4,4,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(5,4,'clinic','Oral','Amlodipine','5','mg','mane','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(6,4,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:33','2026-04-21 11:06:33'),(7,6,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(8,6,'clinic','Oral','Amlodipine','5','mg','mane','30 days',31,31,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(9,6,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:34','2026-04-21 11:06:34'),(10,8,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(11,8,'clinic','Oral','Amlodipine','5','mg','mane','30 days',31,31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(12,8,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(13,10,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(14,10,'clinic','Oral','Amlodipine','5','mg','mane','30 days',31,31,'2026-04-21 11:06:35','2026-04-21 11:06:35'),(15,10,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(16,12,'clinic','Oral','Losartan','50','mg','mane','30 days',31,31,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(17,12,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:36','2026-04-21 11:06:36'),(18,14,'clinic','Oral','Losartan','50','mg','mane','30 days',31,31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(19,14,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(20,16,'clinic','Oral','Losartan','50','mg','mane','30 days',31,31,'2026-04-21 11:06:37','2026-04-21 11:06:37'),(21,16,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(22,18,'clinic','Oral','Losartan','50','mg','mane','30 days',31,31,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(23,18,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:38','2026-04-21 11:06:38'),(24,20,'clinic','Oral','Losartan','50','mg','mane','30 days',31,31,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(25,20,'clinic','Oral','Atorvastatin','20','mg','nocte','30 days',31,31,'2026-04-21 11:06:39','2026-04-21 11:06:39'),(26,22,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(27,22,'clinic','Oral','Aspirin','75','mg','mane','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(28,22,'clinic','Oral','Atorvastatin','40','mg','nocte','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(29,22,'clinic','Oral','Metoprolol','25','mg','bd','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(30,24,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(31,24,'clinic','Oral','Aspirin','75','mg','mane','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(32,24,'clinic','Oral','Atorvastatin','40','mg','nocte','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(33,24,'clinic','Oral','Metoprolol','25','mg','bd','30 days',31,31,'2026-04-21 11:06:40','2026-04-21 11:06:40'),(34,26,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(35,26,'clinic','Oral','Aspirin','75','mg','mane','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(36,26,'clinic','Oral','Atorvastatin','40','mg','nocte','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(37,26,'clinic','Oral','Metoprolol','25','mg','bd','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(38,28,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(39,28,'clinic','Oral','Aspirin','75','mg','mane','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(40,28,'clinic','Oral','Atorvastatin','40','mg','nocte','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(41,28,'clinic','Oral','Metoprolol','25','mg','bd','30 days',31,31,'2026-04-21 11:06:41','2026-04-21 11:06:41'),(42,30,'clinic','Oral','Metformin','500','mg','bd','30 days',31,31,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(43,30,'clinic','Oral','Aspirin','75','mg','mane','30 days',31,31,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(44,30,'clinic','Oral','Atorvastatin','40','mg','nocte','30 days',31,31,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(45,30,'clinic','Oral','Metoprolol','25','mg','bd','30 days',31,31,'2026-04-21 11:06:42','2026-04-21 11:06:42'),(46,32,'clinic','MDI','Salbutamol','100','mcg','SOS',NULL,31,31,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(47,32,'clinic','MDI','Beclomethasone','200','mcg','bd','30 days',31,31,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(48,32,'clinic','Oral','Levothyroxine','50','mcg','mane','30 days',31,31,'2026-04-21 11:06:43','2026-04-21 11:06:43'),(49,34,'clinic','MDI','Salbutamol','100','mcg','SOS',NULL,31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(50,34,'clinic','MDI','Beclomethasone','200','mcg','bd','30 days',31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(51,34,'clinic','Oral','Levothyroxine','50','mcg','mane','30 days',31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(52,36,'clinic','MDI','Salbutamol','100','mcg','SOS',NULL,31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(53,36,'clinic','MDI','Beclomethasone','200','mcg','bd','30 days',31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(54,36,'clinic','Oral','Levothyroxine','50','mcg','mane','30 days',31,31,'2026-04-21 11:06:44','2026-04-21 11:06:44'),(55,38,'clinic','MDI','Salbutamol','100','mcg','SOS',NULL,31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(56,38,'clinic','MDI','Beclomethasone','200','mcg','bd','30 days',31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(57,38,'clinic','Oral','Levothyroxine','50','mcg','mane','30 days',31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(58,40,'clinic','MDI','Salbutamol','100','mcg','SOS',NULL,31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(59,40,'clinic','MDI','Beclomethasone','200','mcg','bd','30 days',31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(60,40,'clinic','Oral','Levothyroxine','50','mcg','mane','30 days',31,31,'2026-04-21 11:06:45','2026-04-21 11:06:45'),(61,42,'clinic','Oral','Amlodipine','10','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(62,42,'clinic','Oral','Furosemide','40','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(63,42,'clinic','Oral','Allopurinol','100','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(64,44,'clinic','Oral','Amlodipine','10','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(65,44,'clinic','Oral','Furosemide','40','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(66,44,'clinic','Oral','Allopurinol','100','mg','mane','30 days',31,31,'2026-04-21 11:06:46','2026-04-21 11:06:46'),(67,46,'clinic','Oral','Amlodipine','10','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(68,46,'clinic','Oral','Furosemide','40','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(69,46,'clinic','Oral','Allopurinol','100','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(70,48,'clinic','Oral','Amlodipine','10','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(71,48,'clinic','Oral','Furosemide','40','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(72,48,'clinic','Oral','Allopurinol','100','mg','mane','30 days',31,31,'2026-04-21 11:06:47','2026-04-21 11:06:47'),(73,50,'clinic','Oral','Amlodipine','10','mg','mane','30 days',31,31,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(74,50,'clinic','Oral','Furosemide','40','mg','mane','30 days',31,31,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(75,50,'clinic','Oral','Allopurinol','100','mg','mane','30 days',31,31,'2026-04-21 11:06:48','2026-04-21 11:06:48'),(76,51,'clinic','Oral','Amlodipine','10','mg','mane','30 days',37,NULL,'2026-04-21 11:39:31','2026-04-21 11:39:31'),(77,51,'clinic','Oral','Furosemide','40','mg','mane','30 days',37,NULL,'2026-04-21 11:39:31','2026-04-21 11:39:31'),(78,51,'clinic','Oral','Allopurinol','100','mg','mane','30 days',37,NULL,'2026-04-21 11:39:31','2026-04-21 11:39:31'),(79,51,'management','Oral','Paracetamol','500','mg','tds','5 days',37,NULL,'2026-04-21 11:42:32','2026-04-21 11:42:32');
/*!40000 ALTER TABLE `visit_drugs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_notes`
--

DROP TABLE IF EXISTS `visit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint(20) unsigned NOT NULL,
  `presenting_complaints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`presenting_complaints`)),
  `complaint_durations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`complaint_durations`)),
  `past_medical_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`past_medical_history`)),
  `past_surgical_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`past_surgical_history`)),
  `social_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_history`)),
  `menstrual_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`menstrual_history`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `general_looking` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`general_looking`)),
  `pulse_rate` smallint(5) unsigned DEFAULT NULL,
  `cardiology_findings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cardiology_findings`)),
  `respiratory_findings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respiratory_findings`)),
  `abdominal_findings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`abdominal_findings`)),
  `neurological_findings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`neurological_findings`)),
  `dermatological_findings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dermatological_findings`)),
  `management_instruction` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_notes_visit_id_unique` (`visit_id`),
  CONSTRAINT `visit_notes_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `clinic_visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_notes`
--

LOCK TABLES `visit_notes` WRITE;
/*!40000 ALTER TABLE `visit_notes` DISABLE KEYS */;
INSERT INTO `visit_notes` VALUES (1,1,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:32','2026-04-21 11:06:32','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(2,2,'[\"Routine review\",\"Diabetes Mellitus Type 2\"]','[\"6 months\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:32','2026-04-21 11:06:32','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(3,3,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:33','2026-04-21 11:06:33','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(4,4,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:33','2026-04-21 11:06:33','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(5,5,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:33','2026-04-21 11:06:33','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(6,6,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:34','2026-04-21 11:06:34','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(7,7,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:35','2026-04-21 11:06:35','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(8,8,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:35','2026-04-21 11:06:35','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(9,9,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:35','2026-04-21 11:06:35','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(10,10,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Hypertension\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:35','2026-04-21 11:06:35','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(11,11,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:36','2026-04-21 11:06:36','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(12,12,'[\"Routine review\",\"Hypertension\"]','[\"6 months\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:36','2026-04-21 11:06:36','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(13,13,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:36','2026-04-21 11:06:36','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(14,14,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:37','2026-04-21 11:06:37','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(15,15,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:37','2026-04-21 11:06:37','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(16,16,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:37','2026-04-21 11:06:37','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(17,17,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:38','2026-04-21 11:06:38','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(18,18,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:38','2026-04-21 11:06:38','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(19,19,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:39','2026-04-21 11:06:39','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(20,20,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:39','2026-04-21 11:06:39','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(21,21,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:39','2026-04-21 11:06:39','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(22,22,'[\"Routine review\",\"Diabetes Mellitus Type 2\"]','[\"6 months\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:39','2026-04-21 11:06:39','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(23,23,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:40','2026-04-21 11:06:40','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(24,24,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:40','2026-04-21 11:06:40','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(25,25,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:40','2026-04-21 11:06:40','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(26,26,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:41','2026-04-21 11:06:41','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(27,27,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:41','2026-04-21 11:06:41','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(28,28,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:41','2026-04-21 11:06:41','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(29,29,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:42','2026-04-21 11:06:42','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(30,30,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Diabetes Mellitus Type 2\",\"Ischemic Heart Disease\",\"Dyslipidaemia\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:42','2026-04-21 11:06:42','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(31,31,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:43','2026-04-21 11:06:43','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(32,32,'[\"Routine review\",\"Asthma\"]','[\"6 months\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:43','2026-04-21 11:06:43','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(33,33,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:43','2026-04-21 11:06:43','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(34,34,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:43','2026-04-21 11:06:43','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(35,35,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:44','2026-04-21 11:06:44','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(36,36,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:44','2026-04-21 11:06:44','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(37,37,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:44','2026-04-21 11:06:44','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(38,38,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:44','2026-04-21 11:06:44','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(39,39,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:45','2026-04-21 11:06:45','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(40,40,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Asthma\",\"Hypothyroidism\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:45','2026-04-21 11:06:45','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(41,41,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:45','2026-04-21 11:06:45','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(42,42,'[\"Routine review\",\"Hypertension\"]','[\"6 months\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:45','2026-04-21 11:06:45','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(43,43,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:46','2026-04-21 11:06:46','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(44,44,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:46','2026-04-21 11:06:46','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(45,45,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:46','2026-04-21 11:06:46','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(46,46,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:46','2026-04-21 11:06:46','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(47,47,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:47','2026-04-21 11:06:47','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(48,48,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:47','2026-04-21 11:06:47','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(49,49,'[\"Routine review\",\"Fatigue\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:47','2026-04-21 11:06:47','[\"Well-looking\",\"Afebrile\"]',NULL,NULL,NULL,NULL,NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(50,50,'[\"Routine review\",\"Follow-up\"]','[\"1 month\"]','[\"Hypertension\",\"Chronic Kidney Disease\",\"Gout\"]',NULL,'[\"Non-smoker\",\"Lives with family\"]',NULL,'2026-04-21 11:06:47','2026-04-21 11:06:47','[\"Well-looking\",\"Afebrile\"]',NULL,'[\"Normal S1 S2 heard\",\"No murmurs\",\"Regular rhythm\"]','[\"Clear air entry bilaterally\"]','[\"Soft and non-tender\",\"No organomegaly\"]',NULL,NULL,'[\"Review in 1 month\",\"Medication compliance is important\"]'),(51,51,'[\"Cough x 3 days\"]','[\"1 month\"]','[]','[]','[]',NULL,'2026-04-21 11:39:31','2026-04-21 11:42:57','[]',NULL,'[]','[]','[]','[]','[]','[]');
/*!40000 ALTER TABLE `visit_notes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-22 16:17:46
