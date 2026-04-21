-- OpenHIMS2 — Health Information Management System
-- Database schema (structure only) + required seed data
--
-- Usage:
--   1. Create a database named `phims` (or any name — update .env accordingly)
--   2. Import this file:  mysql -u root -p phims < database.sql
--   3. Then run:          php artisan db:seed --class=AdminSeeder
--      (or use the full seeder: php artisan migrate:fresh --seed)
--
-- NOTE: unit_templates and view_templates seed data is included below.
--       After import, run AdminSeeder to create the default admin account.
--
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_templates_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `unit_template_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `view_templates_code_unique` (`code`),
  KEY `view_templates_unit_template_id_foreign` (`unit_template_id`),
  CONSTRAINT `view_templates_unit_template_id_foreign` FOREIGN KEY (`unit_template_id`) REFERENCES `unit_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- Seed data: unit_templates (static clinic types)
--

INSERT INTO `unit_templates` VALUES (1,'General Medical Clinic','GMC','2026-02-23 20:46:02','2026-02-23 20:46:02'),(2,'Dental Clinic','DC','2026-02-23 20:46:02','2026-02-23 20:46:02'),(3,'General Inward','GI','2026-02-23 20:46:02','2026-02-23 20:46:02'),(4,'General Pharmacy','GP','2026-02-23 20:46:02','2026-02-23 20:46:02'),(5,'Office','OFFICE','2026-02-23 20:46:02','2026-02-23 20:46:02');

--
-- Seed data: view_templates (static role views per clinic type)
--

INSERT INTO `view_templates` VALUES (1,'GMC - Doctor View','gmc-doctor','clinical.gmc.doctor',1,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(2,'GMC - Clerk View','gmc-clerk','clinical.gmc.clerk',1,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(3,'GMC - Nurse View','gmc-nurse','clinical.gmc.nurse',1,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(4,'DC - Doctor View','dc-doctor','clinical.dc.doctor',2,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(5,'DC - Clerk View','dc-clerk','clinical.dc.clerk',2,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(6,'DC - Nurse View','dc-nurse','clinical.dc.nurse',2,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(7,'GI - Doctor View','gi-doctor','clinical.gi.doctor',3,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(8,'GI - Clerk View','gi-clerk','clinical.gi.clerk',3,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(9,'GI - Nurse View','gi-nurse','clinical.gi.nurse',3,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(10,'GP - Doctor View','gp-doctor','clinical.gp.doctor',4,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(11,'GP - Pharmacist View','gp-pharmacist','clinical.gp.pharmacist',4,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(12,'GP - Clerk View','gp-clerk','clinical.gp.clerk',4,'2026-02-23 20:46:02','2026-02-23 20:46:02'),(13,'Office - Doctor View','office-doctor','clinical.office.doctor',5,'2026-02-23 20:46:03','2026-02-23 20:46:03'),(14,'Office - Nurse View','office-nurse','clinical.office.nurse',5,'2026-02-23 20:46:03','2026-02-23 20:46:03'),(15,'Office - Clerk View','office-clerk','clinical.office.clerk',5,'2026-02-23 20:46:03','2026-02-23 20:46:03');

-- Dump completed on 2026-04-21 13:13:32
