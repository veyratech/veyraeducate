-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: veyraeducate
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
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'[INFO] Selamat Datang di VEYRA EDUCATE','Kami mengucapkan selamat datang di platform VEYRA EDUCATE. Sistem ini dirancang untuk membantu pengelolaan sekolah menjadi lebih digital, efisien, dan terintegrasi. Silakan eksplorasi semua fitur yang tersedia.','2026-11-15 10:48:48',1,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(2,'[MAINTENANCE] Maintenance Server Akhir Pekan','Diberitahukan kepada seluruh pengguna bahwa akan dilakukan maintenance server pada akhir pekan mendatang. Selama periode tersebut, beberapa layanan mungkin tidak dapat diakses. Mohon pengertian dan kerjasamanya.','2026-11-15 10:48:48',1,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(3,'[PROMO] Paket Super Diskon 20%','Promo terbatas! Dapatkan diskon 20% untuk Paket Super (1 Tahun) dengan segala fitur premium dan prioritas support 24/7. Penawaran ini hanya berlaku dalam waktu terbatas. Segera daftarkan sekolah Anda!','2026-11-15 10:48:48',1,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL);
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_guides`
--

DROP TABLE IF EXISTS `app_guides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_guides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `school_id` bigint(20) unsigned DEFAULT NULL,
  `web_app_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'umum',
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `file_type` varchar(20) NOT NULL DEFAULT 'pdf',
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_guides_user_id_foreign` (`user_id`),
  KEY `app_guides_school_id_foreign` (`school_id`),
  KEY `app_guides_web_app_id_foreign` (`web_app_id`),
  CONSTRAINT `app_guides_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `app_guides_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `app_guides_web_app_id_foreign` FOREIGN KEY (`web_app_id`) REFERENCES `web_apps` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_guides`
--

LOCK TABLES `app_guides` WRITE;
/*!40000 ALTER TABLE `app_guides` DISABLE KEYS */;
INSERT INTO `app_guides` VALUES (1,1,NULL,NULL,'aa',NULL,NULL,'umum','guides/fVsjuiBqedjk5ST8FCzBhuCblH0TSbkCsf8FkCOT.pdf','surat tugas rev.pdf',225229,'pdf',0,'2026-11-16 03:48:39','2026-11-16 03:48:39',NULL);
/*!40000 ALTER TABLE `app_guides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_requests`
--

DROP TABLE IF EXISTS `app_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `target_date` date DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_requests_school_id_foreign` (`school_id`),
  KEY `app_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `app_requests_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `app_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_requests`
--

LOCK TABLES `app_requests` WRITE;
/*!40000 ALTER TABLE `app_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('veyra-educate-cache-c5efc69483323d5f47042e4a276a55f5','i:2;',1794822489),('veyra-educate-cache-c5efc69483323d5f47042e4a276a55f5:timer','i:1794822489;',1794822489),('veyra-educate-cache-e00cf25ad42683b3df678c61f42c6bda','i:2;',1794822740),('veyra-educate-cache-e00cf25ad42683b3df678c61f42c6bda:timer','i:1794822740;',1794822740),('veyra-educate-cache-e4a7872e45880e5b25359f0660eba706','i:1;',1794822998),('veyra-educate-cache-e4a7872e45880e5b25359f0660eba706:timer','i:1794822997;',1794822998),('veyra-educate-cache-homepage.sections.active','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:1;s:3:\"key\";s:4:\"hero\";s:5:\"title\";s:32:\"Hero Background (Latar Belakang)\";s:12:\"section_type\";s:4:\"hero\";s:14:\"order_position\";i:1;s:9:\"is_active\";i:1;s:7:\"content\";s:768:\"{\"show_banner\":true,\"lead\":\"Veyra Educate Wadah Super Digital Sekolah Anda\",\"highlight\":null,\"tail\":null,\"description\":\"Veyra Educate menampung aplikasi sekolah Anda dalam satu platform: akun terpusat, langganan transparan, dan dashboard yang siap diisi aplikasi.\",\"primary_btn_text\":\"Daftar sekolah\",\"primary_btn_url\":\"\\/register\",\"secondary_btn_text\":\"Lihat demo\",\"secondary_btn_url\":\"\\/demo\",\"stats\":[{\"number\":\"3\",\"label\":\"Paket\"},{\"number\":\"Cloud\",\"label\":\"Server\"},{\"number\":\"Otomatis\",\"label\":\"Aktivasi\"}],\"bg_type\":\"custom_image\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\",\"image_url\":\"\\/storage\\/homepage\\/kzq0QQy9kSs7SzAtkurMkB0cHdY9wQ5irKcIhLzJ.png\",\"bg_image_url\":\"\\/storage\\/homepage\\/bg\\/0aArDDyh7XI5UEvK1DyC7gpHqTG2boR7UcSINtQw.png\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 07:35:13\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:1;s:3:\"key\";s:4:\"hero\";s:5:\"title\";s:32:\"Hero Background (Latar Belakang)\";s:12:\"section_type\";s:4:\"hero\";s:14:\"order_position\";i:1;s:9:\"is_active\";i:1;s:7:\"content\";s:768:\"{\"show_banner\":true,\"lead\":\"Veyra Educate Wadah Super Digital Sekolah Anda\",\"highlight\":null,\"tail\":null,\"description\":\"Veyra Educate menampung aplikasi sekolah Anda dalam satu platform: akun terpusat, langganan transparan, dan dashboard yang siap diisi aplikasi.\",\"primary_btn_text\":\"Daftar sekolah\",\"primary_btn_url\":\"\\/register\",\"secondary_btn_text\":\"Lihat demo\",\"secondary_btn_url\":\"\\/demo\",\"stats\":[{\"number\":\"3\",\"label\":\"Paket\"},{\"number\":\"Cloud\",\"label\":\"Server\"},{\"number\":\"Otomatis\",\"label\":\"Aktivasi\"}],\"bg_type\":\"custom_image\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\",\"image_url\":\"\\/storage\\/homepage\\/kzq0QQy9kSs7SzAtkurMkB0cHdY9wQ5irKcIhLzJ.png\",\"bg_image_url\":\"\\/storage\\/homepage\\/bg\\/0aArDDyh7XI5UEvK1DyC7gpHqTG2boR7UcSINtQw.png\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 07:35:13\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:2;s:3:\"key\";s:11:\"trust_strip\";s:5:\"title\";s:31:\"Pilar Kepercayaan (Trust Strip)\";s:12:\"section_type\";s:11:\"trust_strip\";s:14:\"order_position\";i:2;s:9:\"is_active\";i:1;s:7:\"content\";s:296:\"{\"items\":[{\"title\":\"Tanpa server lokal\",\"desc\":\"Infrastruktur cloud yang kami kelola sepenuhnya.\",\"color\":\"brand\"},{\"title\":\"Akun aman\",\"desc\":\"Login terpusat untuk seluruh tim sekolah.\",\"color\":\"gold\"},{\"title\":\"Langganan jelas\",\"desc\":\"Harga transparan, aktivasi otomatis.\",\"color\":\"emerald\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:2;s:3:\"key\";s:11:\"trust_strip\";s:5:\"title\";s:31:\"Pilar Kepercayaan (Trust Strip)\";s:12:\"section_type\";s:11:\"trust_strip\";s:14:\"order_position\";i:2;s:9:\"is_active\";i:1;s:7:\"content\";s:296:\"{\"items\":[{\"title\":\"Tanpa server lokal\",\"desc\":\"Infrastruktur cloud yang kami kelola sepenuhnya.\",\"color\":\"brand\"},{\"title\":\"Akun aman\",\"desc\":\"Login terpusat untuk seluruh tim sekolah.\",\"color\":\"gold\"},{\"title\":\"Langganan jelas\",\"desc\":\"Harga transparan, aktivasi otomatis.\",\"color\":\"emerald\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:3;s:3:\"key\";s:8:\"features\";s:5:\"title\";s:39:\"Satu wadah untuk semua aplikasi sekolah\";s:12:\"section_type\";s:8:\"features\";s:14:\"order_position\";i:3;s:9:\"is_active\";i:1;s:7:\"content\";s:1173:\"{\"badge\":\"Fitur VEYRA\",\"title\":\"Satu wadah untuk semua aplikasi sekolah\",\"subtitle\":\"Platform siap pakai: akun, langganan, dan slot aplikasi yang menunggu diisi.\",\"items\":[{\"title\":\"Dashboard sekolah\",\"accent\":\"brand\",\"body\":\"Satu dashboard untuk operator: status langganan, ringkasan, dan akses aplikasi.\",\"image_url\":null},{\"title\":\"Slot aplikasi siap tanam\",\"accent\":\"gold\",\"body\":\"Aplikasi sekolah tinggal diunggah ke wadah \\u2014 tampil di dashboard tanpa daftar ulang.\",\"image_url\":null},{\"title\":\"Multi-sekolah (tenant)\",\"accent\":\"sky\",\"body\":\"Setiap sekolah punya data terisolasi \\u2014 aman untuk banyak mitra.\",\"image_url\":null},{\"title\":\"Akun terpusat\",\"accent\":\"teal\",\"body\":\"Satu kredensial untuk masuk panel dan seluruh aplikasi aktif dalam langganan.\",\"image_url\":null},{\"title\":\"Pembayaran resmi\",\"accent\":\"amber\",\"body\":\"QRIS, virtual account, dan e-wallet via Midtrans \\u2014 aktivasi otomatis setelah lunas.\",\"image_url\":null},{\"title\":\"Panel admin lengkap\",\"accent\":\"emerald\",\"body\":\"Kelola sekolah, pengguna, paket, dan pembayaran dari satu tempat.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 16:50:23\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:3;s:3:\"key\";s:8:\"features\";s:5:\"title\";s:39:\"Satu wadah untuk semua aplikasi sekolah\";s:12:\"section_type\";s:8:\"features\";s:14:\"order_position\";i:3;s:9:\"is_active\";i:1;s:7:\"content\";s:1173:\"{\"badge\":\"Fitur VEYRA\",\"title\":\"Satu wadah untuk semua aplikasi sekolah\",\"subtitle\":\"Platform siap pakai: akun, langganan, dan slot aplikasi yang menunggu diisi.\",\"items\":[{\"title\":\"Dashboard sekolah\",\"accent\":\"brand\",\"body\":\"Satu dashboard untuk operator: status langganan, ringkasan, dan akses aplikasi.\",\"image_url\":null},{\"title\":\"Slot aplikasi siap tanam\",\"accent\":\"gold\",\"body\":\"Aplikasi sekolah tinggal diunggah ke wadah \\u2014 tampil di dashboard tanpa daftar ulang.\",\"image_url\":null},{\"title\":\"Multi-sekolah (tenant)\",\"accent\":\"sky\",\"body\":\"Setiap sekolah punya data terisolasi \\u2014 aman untuk banyak mitra.\",\"image_url\":null},{\"title\":\"Akun terpusat\",\"accent\":\"teal\",\"body\":\"Satu kredensial untuk masuk panel dan seluruh aplikasi aktif dalam langganan.\",\"image_url\":null},{\"title\":\"Pembayaran resmi\",\"accent\":\"amber\",\"body\":\"QRIS, virtual account, dan e-wallet via Midtrans \\u2014 aktivasi otomatis setelah lunas.\",\"image_url\":null},{\"title\":\"Panel admin lengkap\",\"accent\":\"emerald\",\"body\":\"Kelola sekolah, pengguna, paket, dan pembayaran dari satu tempat.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 16:50:23\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:4;s:3:\"key\";s:12:\"app_showcase\";s:5:\"title\";s:44:\"Ragam Aplikasi Siap HADIR untuk Sekolah Anda\";s:12:\"section_type\";s:12:\"app_showcase\";s:14:\"order_position\";i:4;s:9:\"is_active\";i:1;s:7:\"content\";s:2227:\"{\"badge\":\"Katalog Aplikasi\",\"title\":\"Ragam Aplikasi Siap HADIR untuk Sekolah Anda\",\"subtitle\":\"Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah \\u2014 dari ujian daring, presensi, rapor, hingga keuangan.\",\"source_mode\":\"custom\",\"items\":[{\"name\":\"CBT & Ujian Online\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"brand\",\"icon\":\"academic-cap\",\"tags\":\"Anti-Curang, Bank Soal, Koreksi Instan\",\"description\":\"Sistem ujian berbasis komputer anti-curang, acak butir soal & opsi, timer presisi, dan penilaian otomatis.\",\"image_url\":null},{\"name\":\"Presensi & Absensi Siswa\",\"category\":\"Kesiswaan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"emerald\",\"icon\":\"clipboard\",\"tags\":\"QR Code, Kartu RFID, Rekap Bulanan\",\"description\":\"Pencatatan kehadiran digital via barcode\\/QR code & kartu RFID, rekapitulasi kehadiran instan, dan integrasi wali murid.\",\"image_url\":null},{\"name\":\"E-Rapor Kurikulum Merdeka\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"gold\",\"icon\":\"document-text\",\"tags\":\"Kurikulum Merdeka, Cetak PDF, Ledger Nilai\",\"description\":\"Pengisian nilai capaian pembelajaran, ledger nilai kelas otomatis, dan cetak rapor format PDF resmi tanpa ribet.\",\"image_url\":null},{\"name\":\"SPP & Keuangan Sekolah\",\"category\":\"Keuangan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"amber\",\"icon\":\"wallet\",\"tags\":\"Tagihan SPP, Kuitansi Digital, Rekap Kas\",\"description\":\"Manajemen pos tagihan siswa, kuitansi digital otomatis, pelacakan tunggakan, dan rekap arus kas sekolah.\",\"image_url\":null},{\"name\":\"Perpustakaan Digital (E-Perpus)\",\"category\":\"Perpustakaan\",\"status_badge\":\"Bisa Ditanam\",\"accent\":\"sky\",\"icon\":\"book-open\",\"tags\":\"Barcode Buku, Bebas Pustaka, E-Katalog\",\"description\":\"Manajemen katalog buku, sirkulasi peminjaman barcode, bebas pustaka kelulusan, dan statistik pembaca.\",\"image_url\":null},{\"name\":\"SIM Kepegawaian & Jurnal Guru\",\"category\":\"Kepegawaian\",\"status_badge\":\"Segera Hadir\",\"accent\":\"teal\",\"icon\":\"user-group\",\"tags\":\"Agenda Mengajar, Jam Pendidik, Arsip\",\"description\":\"Pencatatan agenda mengajar harian, jam tatap muka pendidik, dan arsip dokumen kepegawaian sekolah.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 16:51:20\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:4;s:3:\"key\";s:12:\"app_showcase\";s:5:\"title\";s:44:\"Ragam Aplikasi Siap HADIR untuk Sekolah Anda\";s:12:\"section_type\";s:12:\"app_showcase\";s:14:\"order_position\";i:4;s:9:\"is_active\";i:1;s:7:\"content\";s:2227:\"{\"badge\":\"Katalog Aplikasi\",\"title\":\"Ragam Aplikasi Siap HADIR untuk Sekolah Anda\",\"subtitle\":\"Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah \\u2014 dari ujian daring, presensi, rapor, hingga keuangan.\",\"source_mode\":\"custom\",\"items\":[{\"name\":\"CBT & Ujian Online\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"brand\",\"icon\":\"academic-cap\",\"tags\":\"Anti-Curang, Bank Soal, Koreksi Instan\",\"description\":\"Sistem ujian berbasis komputer anti-curang, acak butir soal & opsi, timer presisi, dan penilaian otomatis.\",\"image_url\":null},{\"name\":\"Presensi & Absensi Siswa\",\"category\":\"Kesiswaan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"emerald\",\"icon\":\"clipboard\",\"tags\":\"QR Code, Kartu RFID, Rekap Bulanan\",\"description\":\"Pencatatan kehadiran digital via barcode\\/QR code & kartu RFID, rekapitulasi kehadiran instan, dan integrasi wali murid.\",\"image_url\":null},{\"name\":\"E-Rapor Kurikulum Merdeka\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"gold\",\"icon\":\"document-text\",\"tags\":\"Kurikulum Merdeka, Cetak PDF, Ledger Nilai\",\"description\":\"Pengisian nilai capaian pembelajaran, ledger nilai kelas otomatis, dan cetak rapor format PDF resmi tanpa ribet.\",\"image_url\":null},{\"name\":\"SPP & Keuangan Sekolah\",\"category\":\"Keuangan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"amber\",\"icon\":\"wallet\",\"tags\":\"Tagihan SPP, Kuitansi Digital, Rekap Kas\",\"description\":\"Manajemen pos tagihan siswa, kuitansi digital otomatis, pelacakan tunggakan, dan rekap arus kas sekolah.\",\"image_url\":null},{\"name\":\"Perpustakaan Digital (E-Perpus)\",\"category\":\"Perpustakaan\",\"status_badge\":\"Bisa Ditanam\",\"accent\":\"sky\",\"icon\":\"book-open\",\"tags\":\"Barcode Buku, Bebas Pustaka, E-Katalog\",\"description\":\"Manajemen katalog buku, sirkulasi peminjaman barcode, bebas pustaka kelulusan, dan statistik pembaca.\",\"image_url\":null},{\"name\":\"SIM Kepegawaian & Jurnal Guru\",\"category\":\"Kepegawaian\",\"status_badge\":\"Segera Hadir\",\"accent\":\"teal\",\"icon\":\"user-group\",\"tags\":\"Agenda Mengajar, Jam Pendidik, Arsip\",\"description\":\"Pencatatan agenda mengajar harian, jam tatap muka pendidik, dan arsip dokumen kepegawaian sekolah.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-16 16:51:20\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:5;s:3:\"key\";s:8:\"workflow\";s:5:\"title\";s:25:\"Alur Langkah (Cara Kerja)\";s:12:\"section_type\";s:8:\"workflow\";s:14:\"order_position\";i:5;s:9:\"is_active\";i:1;s:7:\"content\";s:708:\"{\"badge\":\"Cara kerja\",\"title\":\"Dari daftar hingga panel aktif dalam empat langkah\",\"subtitle\":\"Alur pembayaran resmi Midtrans \\u2014 QRIS, virtual account, dan e-wallet \\u2014 lalu aktivasi otomatis.\",\"steps\":[{\"title\":\"Pilih paket\",\"body\":\"Tentukan durasi langganan \\u2014 Semesteran, Tahunan, atau Super \\u2014 sesuai kebutuhan sekolah.\"},{\"title\":\"Bayar resmi\",\"body\":\"Selesaikan pembayaran melalui QRIS, virtual account, atau e-wallet pada halaman checkout.\"},{\"title\":\"Otomatis aktif\",\"body\":\"Sistem memverifikasi pembayaran dan mengaktifkan panel sekolah beserta masa berlakunya.\"},{\"title\":\"Buka aplikasi\",\"body\":\"Masuk ke dashboard, buka aplikasi sekolah Anda \\u2014 siap dipakai tim di lapangan.\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:5;s:3:\"key\";s:8:\"workflow\";s:5:\"title\";s:25:\"Alur Langkah (Cara Kerja)\";s:12:\"section_type\";s:8:\"workflow\";s:14:\"order_position\";i:5;s:9:\"is_active\";i:1;s:7:\"content\";s:708:\"{\"badge\":\"Cara kerja\",\"title\":\"Dari daftar hingga panel aktif dalam empat langkah\",\"subtitle\":\"Alur pembayaran resmi Midtrans \\u2014 QRIS, virtual account, dan e-wallet \\u2014 lalu aktivasi otomatis.\",\"steps\":[{\"title\":\"Pilih paket\",\"body\":\"Tentukan durasi langganan \\u2014 Semesteran, Tahunan, atau Super \\u2014 sesuai kebutuhan sekolah.\"},{\"title\":\"Bayar resmi\",\"body\":\"Selesaikan pembayaran melalui QRIS, virtual account, atau e-wallet pada halaman checkout.\"},{\"title\":\"Otomatis aktif\",\"body\":\"Sistem memverifikasi pembayaran dan mengaktifkan panel sekolah beserta masa berlakunya.\"},{\"title\":\"Buka aplikasi\",\"body\":\"Masuk ke dashboard, buka aplikasi sekolah Anda \\u2014 siap dipakai tim di lapangan.\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:6;s:3:\"key\";s:9:\"ecosystem\";s:5:\"title\";s:20:\"Tiga Pilar Ekosistem\";s:12:\"section_type\";s:9:\"ecosystem\";s:14:\"order_position\";i:6;s:9:\"is_active\";i:1;s:7:\"content\";s:681:\"{\"badge\":\"Ekosistem\",\"title\":\"Berbayar resmi, aplikasi bertambah, akses terpusat\",\"subtitle\":\"Tiga pilar layanan yang menemani sekolah sepanjang langganan.\",\"items\":[{\"title\":\"Pembayaran & aktivasi otomatis\",\"body\":\"QRIS, virtual account bank, dan e-wallet. Sistem memverifikasi pembayaran dan mengaktifkan lisensi tanpa campur tangan admin.\",\"accent\":\"brand\"},{\"title\":\"Aplikasi yang terus bertambah\",\"body\":\"Aplikasi sekolah baru diunggah ke wadah dan langsung tersedia di panel pelanggan tanpa daftar ulang.\",\"accent\":\"gold\"},{\"title\":\"Akses terpusat (SSO)\",\"body\":\"Satu kredensial operator untuk panel sekolah dan seluruh aplikasi yang aktif dalam langganan.\",\"accent\":\"sky\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:6;s:3:\"key\";s:9:\"ecosystem\";s:5:\"title\";s:20:\"Tiga Pilar Ekosistem\";s:12:\"section_type\";s:9:\"ecosystem\";s:14:\"order_position\";i:6;s:9:\"is_active\";i:1;s:7:\"content\";s:681:\"{\"badge\":\"Ekosistem\",\"title\":\"Berbayar resmi, aplikasi bertambah, akses terpusat\",\"subtitle\":\"Tiga pilar layanan yang menemani sekolah sepanjang langganan.\",\"items\":[{\"title\":\"Pembayaran & aktivasi otomatis\",\"body\":\"QRIS, virtual account bank, dan e-wallet. Sistem memverifikasi pembayaran dan mengaktifkan lisensi tanpa campur tangan admin.\",\"accent\":\"brand\"},{\"title\":\"Aplikasi yang terus bertambah\",\"body\":\"Aplikasi sekolah baru diunggah ke wadah dan langsung tersedia di panel pelanggan tanpa daftar ulang.\",\"accent\":\"gold\"},{\"title\":\"Akses terpusat (SSO)\",\"body\":\"Satu kredensial operator untuk panel sekolah dan seluruh aplikasi yang aktif dalam langganan.\",\"accent\":\"sky\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:6;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:7;s:3:\"key\";s:7:\"pricing\";s:5:\"title\";s:18:\"Daftar Paket Harga\";s:12:\"section_type\";s:7:\"pricing\";s:14:\"order_position\";i:7;s:9:\"is_active\";i:1;s:7:\"content\";s:164:\"{\"badge\":\"Harga\",\"title\":\"Paket langganan transparan\",\"subtitle\":\"Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi.\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:7;s:3:\"key\";s:7:\"pricing\";s:5:\"title\";s:18:\"Daftar Paket Harga\";s:12:\"section_type\";s:7:\"pricing\";s:14:\"order_position\";i:7;s:9:\"is_active\";i:1;s:7:\"content\";s:164:\"{\"badge\":\"Harga\",\"title\":\"Paket langganan transparan\",\"subtitle\":\"Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi.\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:7;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:8;s:3:\"key\";s:12:\"testimonials\";s:5:\"title\";s:26:\"Testimoni & Ulasan Sekolah\";s:12:\"section_type\";s:12:\"testimonials\";s:14:\"order_position\";i:8;s:9:\"is_active\";i:1;s:7:\"content\";s:1133:\"{\"badge\":\"Testimoni\",\"title\":\"Dipercaya oleh puluhan sekolah & yayasan di Indonesia\",\"subtitle\":\"Pengalaman nyata kepala sekolah dan tim operator yang merasakan kemudahan sistem terpusat Veyra Educate.\",\"items\":[{\"name\":\"Drs. H. Mulyadi, M.Pd.\",\"role\":\"Kepala Sekolah\",\"school\":\"SMA Bina Bangsa\",\"quote\":\"Dulu kami pusing mengelola server ujian sendiri setiap kali semesteran. Sejak memakai Veyra Educate, semuanya berjalan di cloud tanpa takut down, dan pembayarannya sangat transparan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Siti Rahmawati, S.Kom.\",\"role\":\"Koordinator IT & Kurikulum\",\"school\":\"SMK Prestasi Nusantara\",\"quote\":\"Integrasi akun terpusatnya sangat menghemat waktu. Guru tidak perlu menghafal banyak password untuk tiap modul, dan laporan langsung tersinkron ke dashboard pimpinan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Ahmad Fauzi, S.T.\",\"role\":\"Operator Sekolah & Dapodik\",\"school\":\"SMP Islam Al-Hikmah\",\"quote\":\"Aktivasi otomatis Midtrans membuat kami bisa langsung mengoperasikan sistem dalam hitungan menit setelah transfer. Layanan pendampingannya juga sangat ramah dan sigap.\",\"rating\":5,\"avatar_url\":null}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:8;s:3:\"key\";s:12:\"testimonials\";s:5:\"title\";s:26:\"Testimoni & Ulasan Sekolah\";s:12:\"section_type\";s:12:\"testimonials\";s:14:\"order_position\";i:8;s:9:\"is_active\";i:1;s:7:\"content\";s:1133:\"{\"badge\":\"Testimoni\",\"title\":\"Dipercaya oleh puluhan sekolah & yayasan di Indonesia\",\"subtitle\":\"Pengalaman nyata kepala sekolah dan tim operator yang merasakan kemudahan sistem terpusat Veyra Educate.\",\"items\":[{\"name\":\"Drs. H. Mulyadi, M.Pd.\",\"role\":\"Kepala Sekolah\",\"school\":\"SMA Bina Bangsa\",\"quote\":\"Dulu kami pusing mengelola server ujian sendiri setiap kali semesteran. Sejak memakai Veyra Educate, semuanya berjalan di cloud tanpa takut down, dan pembayarannya sangat transparan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Siti Rahmawati, S.Kom.\",\"role\":\"Koordinator IT & Kurikulum\",\"school\":\"SMK Prestasi Nusantara\",\"quote\":\"Integrasi akun terpusatnya sangat menghemat waktu. Guru tidak perlu menghafal banyak password untuk tiap modul, dan laporan langsung tersinkron ke dashboard pimpinan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Ahmad Fauzi, S.T.\",\"role\":\"Operator Sekolah & Dapodik\",\"school\":\"SMP Islam Al-Hikmah\",\"quote\":\"Aktivasi otomatis Midtrans membuat kami bisa langsung mengoperasikan sistem dalam hitungan menit setelah transfer. Layanan pendampingannya juga sangat ramah dan sigap.\",\"rating\":5,\"avatar_url\":null}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:8;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:9;s:3:\"key\";s:3:\"faq\";s:5:\"title\";s:17:\"Tanya Jawab (FAQ)\";s:12:\"section_type\";s:3:\"faq\";s:14:\"order_position\";i:9;s:9:\"is_active\";i:1;s:7:\"content\";s:765:\"{\"badge\":\"FAQ\",\"title\":\"Pertanyaan yang sering diajukan\",\"subtitle\":\"Ringkasan jawaban \\u2014 versi lengkap ada di halaman FAQ.\",\"items\":[{\"q\":\"Bagaimana cara kerja sistem setelah pembayaran?\",\"a\":\"Setelah pembayaran terverifikasi otomatis, status akun berubah menjadi Aktif dan masa berlaku dihitung seketika.\"},{\"q\":\"Apakah sekolah perlu menyiapkan server sendiri?\",\"a\":\"Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.\"},{\"q\":\"Bagaimana jika pembayaran pending atau gagal?\",\"a\":\"Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru.\"},{\"q\":\"Bisakah berganti paket setelah mendaftar?\",\"a\":\"Bisa, selama lisensi belum aktif. Total tagihan menyesuaikan otomatis sebelum pembayaran.\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:9;s:3:\"key\";s:3:\"faq\";s:5:\"title\";s:17:\"Tanya Jawab (FAQ)\";s:12:\"section_type\";s:3:\"faq\";s:14:\"order_position\";i:9;s:9:\"is_active\";i:1;s:7:\"content\";s:765:\"{\"badge\":\"FAQ\",\"title\":\"Pertanyaan yang sering diajukan\",\"subtitle\":\"Ringkasan jawaban \\u2014 versi lengkap ada di halaman FAQ.\",\"items\":[{\"q\":\"Bagaimana cara kerja sistem setelah pembayaran?\",\"a\":\"Setelah pembayaran terverifikasi otomatis, status akun berubah menjadi Aktif dan masa berlaku dihitung seketika.\"},{\"q\":\"Apakah sekolah perlu menyiapkan server sendiri?\",\"a\":\"Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.\"},{\"q\":\"Bagaimana jika pembayaran pending atau gagal?\",\"a\":\"Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru.\"},{\"q\":\"Bisakah berganti paket setelah mendaftar?\",\"a\":\"Bisa, selama lisensi belum aktif. Total tagihan menyesuaikan otomatis sebelum pembayaran.\"}]}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:9;O:26:\"App\\Models\\HomepageSection\":34:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"homepage_sections\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:12:\"\0*\0refreshes\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:10;s:3:\"key\";s:3:\"cta\";s:5:\"title\";s:24:\"Call To Action (Penutup)\";s:12:\"section_type\";s:3:\"cta\";s:14:\"order_position\";i:10;s:9:\"is_active\";i:1;s:7:\"content\";s:320:\"{\"badge\":\"Siap mulai\",\"title\":\"Siap menyiapkan wadah aplikasi sekolah Anda?\",\"subtitle\":\"Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.\",\"btn_text\":\"Daftar sekolah\",\"btn_url\":\"\\/register\",\"wa_text\":\"Konsultasi WhatsApp\",\"wa_url\":\"https:\\/\\/wa.me\\/6285707074778\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:10;s:3:\"key\";s:3:\"cta\";s:5:\"title\";s:24:\"Call To Action (Penutup)\";s:12:\"section_type\";s:3:\"cta\";s:14:\"order_position\";i:10;s:9:\"is_active\";i:1;s:7:\"content\";s:320:\"{\"badge\":\"Siap mulai\",\"title\":\"Siap menyiapkan wadah aplikasi sekolah Anda?\",\"subtitle\":\"Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.\",\"btn_text\":\"Daftar sekolah\",\"btn_url\":\"\\/register\",\"wa_text\":\"Konsultasi WhatsApp\",\"wa_url\":\"https:\\/\\/wa.me\\/6285707074778\"}\";s:10:\"created_at\";s:19:\"2026-11-15 17:48:48\";s:10:\"updated_at\";s:19:\"2026-11-15 17:48:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:9:\"is_active\";s:7:\"boolean\";s:14:\"order_position\";s:7:\"integer\";s:7:\"content\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"key\";i:1;s:5:\"title\";i:2;s:12:\"section_type\";i:3;s:14:\"order_position\";i:4;s:9:\"is_active\";i:5;s:7:\"content\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1794822999);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deployments`
--

DROP TABLE IF EXISTS `deployments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deployments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `web_app_id` bigint(20) unsigned NOT NULL,
  `slug` varchar(255) NOT NULL,
  `version` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `source_file` varchar(255) DEFAULT NULL,
  `file_count` int(11) NOT NULL DEFAULT 0,
  `size_bytes` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `deployed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deployments_web_app_id_foreign` (`web_app_id`),
  KEY `deployments_created_by_foreign` (`created_by`),
  CONSTRAINT `deployments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deployments_web_app_id_foreign` FOREIGN KEY (`web_app_id`) REFERENCES `web_apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deployments`
--

LOCK TABLES `deployments` WRITE;
/*!40000 ALTER TABLE `deployments` DISABLE KEYS */;
/*!40000 ALTER TABLE `deployments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `homepage_sections`
--

DROP TABLE IF EXISTS `homepage_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `homepage_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `section_type` varchar(255) NOT NULL,
  `order_position` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `homepage_sections_key_unique` (`key`),
  KEY `homepage_sections_section_type_index` (`section_type`),
  KEY `homepage_sections_order_position_index` (`order_position`),
  KEY `homepage_sections_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_sections`
--

LOCK TABLES `homepage_sections` WRITE;
/*!40000 ALTER TABLE `homepage_sections` DISABLE KEYS */;
INSERT INTO `homepage_sections` VALUES (1,'hero','Hero Background (Latar Belakang)','hero',1,1,'{\"show_banner\":true,\"lead\":\"Veyra Educate Wadah Super Digital Sekolah Anda\",\"highlight\":null,\"tail\":null,\"description\":\"Veyra Educate menampung aplikasi sekolah Anda dalam satu platform: akun terpusat, langganan transparan, dan dashboard yang siap diisi aplikasi.\",\"primary_btn_text\":\"Daftar sekolah\",\"primary_btn_url\":\"\\/register\",\"secondary_btn_text\":\"Lihat demo\",\"secondary_btn_url\":\"\\/demo\",\"stats\":[{\"number\":\"3\",\"label\":\"Paket\"},{\"number\":\"Cloud\",\"label\":\"Server\"},{\"number\":\"Otomatis\",\"label\":\"Aktivasi\"}],\"bg_type\":\"custom_image\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\",\"image_url\":\"\\/storage\\/homepage\\/kzq0QQy9kSs7SzAtkurMkB0cHdY9wQ5irKcIhLzJ.png\",\"bg_image_url\":\"\\/storage\\/homepage\\/bg\\/0aArDDyh7XI5UEvK1DyC7gpHqTG2boR7UcSINtQw.png\"}','2026-11-15 10:48:48','2026-11-16 00:35:13'),(2,'trust_strip','Pilar Kepercayaan (Trust Strip)','trust_strip',2,1,'{\"items\":[{\"title\":\"Tanpa server lokal\",\"desc\":\"Infrastruktur cloud yang kami kelola sepenuhnya.\",\"color\":\"brand\"},{\"title\":\"Akun aman\",\"desc\":\"Login terpusat untuk seluruh tim sekolah.\",\"color\":\"gold\"},{\"title\":\"Langganan jelas\",\"desc\":\"Harga transparan, aktivasi otomatis.\",\"color\":\"emerald\"}]}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(3,'features','Satu wadah untuk semua aplikasi sekolah','features',3,1,'{\"badge\":\"Fitur VEYRA\",\"title\":\"Satu wadah untuk semua aplikasi sekolah\",\"subtitle\":\"Platform siap pakai: akun, langganan, dan slot aplikasi yang menunggu diisi.\",\"items\":[{\"title\":\"Dashboard sekolah\",\"accent\":\"brand\",\"body\":\"Satu dashboard untuk operator: status langganan, ringkasan, dan akses aplikasi.\",\"image_url\":null},{\"title\":\"Slot aplikasi siap tanam\",\"accent\":\"gold\",\"body\":\"Aplikasi sekolah tinggal diunggah ke wadah \\u2014 tampil di dashboard tanpa daftar ulang.\",\"image_url\":null},{\"title\":\"Multi-sekolah (tenant)\",\"accent\":\"sky\",\"body\":\"Setiap sekolah punya data terisolasi \\u2014 aman untuk banyak mitra.\",\"image_url\":null},{\"title\":\"Akun terpusat\",\"accent\":\"teal\",\"body\":\"Satu kredensial untuk masuk panel dan seluruh aplikasi aktif dalam langganan.\",\"image_url\":null},{\"title\":\"Pembayaran resmi\",\"accent\":\"amber\",\"body\":\"QRIS, virtual account, dan e-wallet via Midtrans \\u2014 aktivasi otomatis setelah lunas.\",\"image_url\":null},{\"title\":\"Panel admin lengkap\",\"accent\":\"emerald\",\"body\":\"Kelola sekolah, pengguna, paket, dan pembayaran dari satu tempat.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}','2026-11-15 10:48:48','2026-11-16 09:50:23'),(4,'app_showcase','Ragam Aplikasi Siap HADIR untuk Sekolah Anda','app_showcase',4,1,'{\"badge\":\"Katalog Aplikasi\",\"title\":\"Ragam Aplikasi Siap HADIR untuk Sekolah Anda\",\"subtitle\":\"Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah \\u2014 dari ujian daring, presensi, rapor, hingga keuangan.\",\"source_mode\":\"custom\",\"items\":[{\"name\":\"CBT & Ujian Online\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"brand\",\"icon\":\"academic-cap\",\"tags\":\"Anti-Curang, Bank Soal, Koreksi Instan\",\"description\":\"Sistem ujian berbasis komputer anti-curang, acak butir soal & opsi, timer presisi, dan penilaian otomatis.\",\"image_url\":null},{\"name\":\"Presensi & Absensi Siswa\",\"category\":\"Kesiswaan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"emerald\",\"icon\":\"clipboard\",\"tags\":\"QR Code, Kartu RFID, Rekap Bulanan\",\"description\":\"Pencatatan kehadiran digital via barcode\\/QR code & kartu RFID, rekapitulasi kehadiran instan, dan integrasi wali murid.\",\"image_url\":null},{\"name\":\"E-Rapor Kurikulum Merdeka\",\"category\":\"Akademik\",\"status_badge\":\"Siap Pakai\",\"accent\":\"gold\",\"icon\":\"document-text\",\"tags\":\"Kurikulum Merdeka, Cetak PDF, Ledger Nilai\",\"description\":\"Pengisian nilai capaian pembelajaran, ledger nilai kelas otomatis, dan cetak rapor format PDF resmi tanpa ribet.\",\"image_url\":null},{\"name\":\"SPP & Keuangan Sekolah\",\"category\":\"Keuangan\",\"status_badge\":\"Siap Pakai\",\"accent\":\"amber\",\"icon\":\"wallet\",\"tags\":\"Tagihan SPP, Kuitansi Digital, Rekap Kas\",\"description\":\"Manajemen pos tagihan siswa, kuitansi digital otomatis, pelacakan tunggakan, dan rekap arus kas sekolah.\",\"image_url\":null},{\"name\":\"Perpustakaan Digital (E-Perpus)\",\"category\":\"Perpustakaan\",\"status_badge\":\"Bisa Ditanam\",\"accent\":\"sky\",\"icon\":\"book-open\",\"tags\":\"Barcode Buku, Bebas Pustaka, E-Katalog\",\"description\":\"Manajemen katalog buku, sirkulasi peminjaman barcode, bebas pustaka kelulusan, dan statistik pembaca.\",\"image_url\":null},{\"name\":\"SIM Kepegawaian & Jurnal Guru\",\"category\":\"Kepegawaian\",\"status_badge\":\"Segera Hadir\",\"accent\":\"teal\",\"icon\":\"user-group\",\"tags\":\"Agenda Mengajar, Jam Pendidik, Arsip\",\"description\":\"Pencatatan agenda mengajar harian, jam tatap muka pendidik, dan arsip dokumen kepegawaian sekolah.\",\"image_url\":null}],\"bg_type\":\"default\",\"bg_color\":\"#0f172a\",\"bg_overlay\":\"dark-60\",\"text_mode\":\"auto\"}','2026-11-15 10:48:48','2026-11-16 09:51:20'),(5,'workflow','Alur Langkah (Cara Kerja)','workflow',5,1,'{\"badge\":\"Cara kerja\",\"title\":\"Dari daftar hingga panel aktif dalam empat langkah\",\"subtitle\":\"Alur pembayaran resmi Midtrans \\u2014 QRIS, virtual account, dan e-wallet \\u2014 lalu aktivasi otomatis.\",\"steps\":[{\"title\":\"Pilih paket\",\"body\":\"Tentukan durasi langganan \\u2014 Semesteran, Tahunan, atau Super \\u2014 sesuai kebutuhan sekolah.\"},{\"title\":\"Bayar resmi\",\"body\":\"Selesaikan pembayaran melalui QRIS, virtual account, atau e-wallet pada halaman checkout.\"},{\"title\":\"Otomatis aktif\",\"body\":\"Sistem memverifikasi pembayaran dan mengaktifkan panel sekolah beserta masa berlakunya.\"},{\"title\":\"Buka aplikasi\",\"body\":\"Masuk ke dashboard, buka aplikasi sekolah Anda \\u2014 siap dipakai tim di lapangan.\"}]}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(6,'ecosystem','Tiga Pilar Ekosistem','ecosystem',6,1,'{\"badge\":\"Ekosistem\",\"title\":\"Berbayar resmi, aplikasi bertambah, akses terpusat\",\"subtitle\":\"Tiga pilar layanan yang menemani sekolah sepanjang langganan.\",\"items\":[{\"title\":\"Pembayaran & aktivasi otomatis\",\"body\":\"QRIS, virtual account bank, dan e-wallet. Sistem memverifikasi pembayaran dan mengaktifkan lisensi tanpa campur tangan admin.\",\"accent\":\"brand\"},{\"title\":\"Aplikasi yang terus bertambah\",\"body\":\"Aplikasi sekolah baru diunggah ke wadah dan langsung tersedia di panel pelanggan tanpa daftar ulang.\",\"accent\":\"gold\"},{\"title\":\"Akses terpusat (SSO)\",\"body\":\"Satu kredensial operator untuk panel sekolah dan seluruh aplikasi yang aktif dalam langganan.\",\"accent\":\"sky\"}]}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(7,'pricing','Daftar Paket Harga','pricing',7,1,'{\"badge\":\"Harga\",\"title\":\"Paket langganan transparan\",\"subtitle\":\"Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi.\"}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(8,'testimonials','Testimoni & Ulasan Sekolah','testimonials',8,1,'{\"badge\":\"Testimoni\",\"title\":\"Dipercaya oleh puluhan sekolah & yayasan di Indonesia\",\"subtitle\":\"Pengalaman nyata kepala sekolah dan tim operator yang merasakan kemudahan sistem terpusat Veyra Educate.\",\"items\":[{\"name\":\"Drs. H. Mulyadi, M.Pd.\",\"role\":\"Kepala Sekolah\",\"school\":\"SMA Bina Bangsa\",\"quote\":\"Dulu kami pusing mengelola server ujian sendiri setiap kali semesteran. Sejak memakai Veyra Educate, semuanya berjalan di cloud tanpa takut down, dan pembayarannya sangat transparan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Siti Rahmawati, S.Kom.\",\"role\":\"Koordinator IT & Kurikulum\",\"school\":\"SMK Prestasi Nusantara\",\"quote\":\"Integrasi akun terpusatnya sangat menghemat waktu. Guru tidak perlu menghafal banyak password untuk tiap modul, dan laporan langsung tersinkron ke dashboard pimpinan.\",\"rating\":5,\"avatar_url\":null},{\"name\":\"Ahmad Fauzi, S.T.\",\"role\":\"Operator Sekolah & Dapodik\",\"school\":\"SMP Islam Al-Hikmah\",\"quote\":\"Aktivasi otomatis Midtrans membuat kami bisa langsung mengoperasikan sistem dalam hitungan menit setelah transfer. Layanan pendampingannya juga sangat ramah dan sigap.\",\"rating\":5,\"avatar_url\":null}]}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(9,'faq','Tanya Jawab (FAQ)','faq',9,1,'{\"badge\":\"FAQ\",\"title\":\"Pertanyaan yang sering diajukan\",\"subtitle\":\"Ringkasan jawaban \\u2014 versi lengkap ada di halaman FAQ.\",\"items\":[{\"q\":\"Bagaimana cara kerja sistem setelah pembayaran?\",\"a\":\"Setelah pembayaran terverifikasi otomatis, status akun berubah menjadi Aktif dan masa berlaku dihitung seketika.\"},{\"q\":\"Apakah sekolah perlu menyiapkan server sendiri?\",\"a\":\"Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.\"},{\"q\":\"Bagaimana jika pembayaran pending atau gagal?\",\"a\":\"Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru.\"},{\"q\":\"Bisakah berganti paket setelah mendaftar?\",\"a\":\"Bisa, selama lisensi belum aktif. Total tagihan menyesuaikan otomatis sebelum pembayaran.\"}]}','2026-11-15 10:48:48','2026-11-15 10:48:48'),(10,'cta','Call To Action (Penutup)','cta',10,1,'{\"badge\":\"Siap mulai\",\"title\":\"Siap menyiapkan wadah aplikasi sekolah Anda?\",\"subtitle\":\"Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.\",\"btn_text\":\"Daftar sekolah\",\"btn_url\":\"\\/register\",\"wa_text\":\"Konsultasi WhatsApp\",\"wa_url\":\"https:\\/\\/wa.me\\/6285707074778\"}','2026-11-15 10:48:48','2026-11-15 10:48:48');
/*!40000 ALTER TABLE `homepage_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (30,'2026_10_31_040000_add_manual_transfer_fields_to_payment_orders_table',1),(31,'2026_11_16_000000_create_marketing_pages_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `payment_orders`
--

DROP TABLE IF EXISTS `payment_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `school_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `amount_idr` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `snap_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`snap_response`)),
  `webhook_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_payload`)),
  `proof_path` varchar(255) DEFAULT NULL,
  `bank_sender` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_orders_order_id_unique` (`order_id`),
  KEY `payment_orders_subscription_id_foreign` (`subscription_id`),
  KEY `payment_orders_school_id_foreign` (`school_id`),
  KEY `payment_orders_user_id_foreign` (`user_id`),
  KEY `payment_orders_plan_id_foreign` (`plan_id`),
  KEY `payment_orders_status_index` (`status`),
  KEY `payment_orders_transaction_id_index` (`transaction_id`),
  CONSTRAINT `payment_orders_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  CONSTRAINT `payment_orders_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_orders_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_orders`
--

LOCK TABLES `payment_orders` WRITE;
/*!40000 ALTER TABLE `payment_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
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
-- Table structure for table `plan_web_apps`
--

DROP TABLE IF EXISTS `plan_web_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan_web_apps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint(20) unsigned NOT NULL,
  `web_app_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_web_apps_plan_id_web_app_id_unique` (`plan_id`,`web_app_id`),
  KEY `plan_web_apps_plan_id_index` (`plan_id`),
  KEY `plan_web_apps_web_app_id_index` (`web_app_id`),
  CONSTRAINT `plan_web_apps_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plan_web_apps_web_app_id_foreign` FOREIGN KEY (`web_app_id`) REFERENCES `web_apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_web_apps`
--

LOCK TABLES `plan_web_apps` WRITE;
/*!40000 ALTER TABLE `plan_web_apps` DISABLE KEYS */;
/*!40000 ALTER TABLE `plan_web_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_idr` bigint(20) unsigned NOT NULL,
  `duration_months` smallint(5) unsigned NOT NULL,
  `duration_label` varchar(255) NOT NULL,
  `badge` varchar(255) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`features`)),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (1,'semester','Paket Semesteran','Akses panel sekolah untuk 1 semester ajaran.',600000,6,'6 Bulan','Masa Aktif: 6 Bulan','[\"Akses panel sekolah (aplikasi terintegrasi)\",\"Akun operator & guru tanpa batas\",\"Aplikasi sekolah terhubung ke satu dashboard\",\"Dukungan onboarding via WhatsApp\"]',0,10,1,0,'2026-11-15 10:48:46','2026-11-15 10:48:46'),(2,'tahunan','Paket 1 Tahun Penuh','Akses penuh mencakup Semester Ganjil & Genap.',1100000,12,'12 Bulan','Masa Aktif: 12 Bulan','[\"Semua fitur Paket Semester\",\"Masa berlaku 1 tahun penuh\",\"Pembaruan aplikasi selama masa aktif\",\"Prioritas support WhatsApp\"]',0,20,1,0,'2026-11-15 10:48:46','2026-11-15 10:48:46'),(3,'super','Paket Super (1 Tahun)','Solusi lengkap untuk kebutuhan digital sekolah.',1500000,12,'12 Bulan + Ekstra','Semua Fitur + Support Prioritas','[\"Seluruh fitur Paket 1 Tahun Penuh\",\"Custom branding sekolah\",\"Onboarding & pelatihan tim\",\"Priority support 24\\/7\"]',1,30,1,1,'2026-11-15 10:48:46','2026-11-15 10:48:46');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_web_apps`
--

DROP TABLE IF EXISTS `school_web_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_web_apps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `web_app_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_web_apps_school_id_web_app_id_unique` (`school_id`,`web_app_id`),
  KEY `school_web_apps_web_app_id_foreign` (`web_app_id`),
  KEY `school_web_apps_school_id_index` (`school_id`),
  CONSTRAINT `school_web_apps_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_web_apps_web_app_id_foreign` FOREIGN KEY (`web_app_id`) REFERENCES `web_apps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_web_apps`
--

LOCK TABLES `school_web_apps` WRITE;
/*!40000 ALTER TABLE `school_web_apps` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_web_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `npsn` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `postal_code` varchar(16) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_app_access_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schools_npsn_unique` (`npsn`),
  KEY `schools_owner_user_id_foreign` (`owner_user_id`),
  CONSTRAINT `schools_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('uPdzxIPB6uCgByAo1B7DvJdmLiz3gfGdOI1oZc9s',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJINW9mN0FGR3F0UWlWbGxWMllBd2FCTDJqMHUwd0QxYzZBRFJxQVd2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvdmV5cmFlZHVjYXRlIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDgxMGQ4OTAyMTFjMGMyODVjMGFmYzcwZmRiYjE2MWViYTIyYTdjYTc2NTQ0NDFhMzU1MTM3MmE1MWZlNzU1NiJ9',1794822940);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_school_id_status_index` (`school_id`,`status`),
  KEY `subscriptions_ends_at_index` (`ends_at`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  CONSTRAINT `subscriptions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'school',
  `school_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_school_id_foreign` (`school_id`),
  KEY `users_role_index` (`role`),
  CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Veyra','admin@veyraeducate.test','2026-11-15 10:48:46','$2y$12$ZcGNewf1jUWOLjf8h75E.uGPq9atBaBFkoebBxg9Cef0V2AeME4AW','super_admin',NULL,NULL,NULL,NULL,'2026-11-15 10:48:47','2026-11-15 10:48:47'),(2,'Super Admin','superadmin@veyraeducate.test','2026-11-15 10:48:47','$2y$12$AzJvJJKLZGrjQSEorTYa2eA.DIinGNXXx5Civ0cJRiLwEoeSFOi12','super_admin',NULL,NULL,NULL,NULL,'2026-11-15 10:48:48','2026-11-15 10:48:48'),(3,'Test User','test@example.com','2026-11-15 10:48:48','$2y$12$.Ny0vep.roP0.YB2qDmldeyxCb0uYfQ.LOtf.Lzuq5n95.7B/Pk.y','school',NULL,NULL,NULL,NULL,'2026-11-15 10:48:49','2026-11-15 10:48:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_apps`
--

DROP TABLE IF EXISTS `web_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_apps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'umum',
  `icon` varchar(255) NOT NULL DEFAULT 'app',
  `access_url` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `source_file` varchar(255) DEFAULT NULL,
  `file_count` int(11) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_apps_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_apps`
--

LOCK TABLES `web_apps` WRITE;
/*!40000 ALTER TABLE `web_apps` DISABLE KEYS */;
INSERT INTO `web_apps` VALUES (1,'cbtonline','CBT Online','Sistem Ujian Online Terpadu, bank soal, dan evaluasi hasil belajar berbasis CBT.','akademik','academic-cap',NULL,'1.0.0',NULL,0,1,1,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(2,'presensi','Presensi Digital','Absensi siswa & guru berbasis QR Code','kesiswaan','clipboard',NULL,NULL,NULL,0,1,2,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(3,'e-rapor','E-Rapor','Pengelolaan rapor digital Kurikulum Merdeka','akademik','document-text',NULL,NULL,NULL,0,1,3,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(4,'spp-keuangan','Keuangan & SPP','Manajemen pembayaran SPP dan keuangan sekolah','keuangan','wallet',NULL,NULL,NULL,0,1,4,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(5,'perpustakaan','Perpustakaan Digital','Katalog dan sirkulasi buku digital','perpustakaan','book-open',NULL,NULL,NULL,0,1,5,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL),(6,'ekskul','Ekstrakurikuler','Manajemen kegiatan ekstrakurikuler siswa','ekskul','trophy',NULL,NULL,NULL,0,1,6,'2026-11-15 10:48:48','2026-11-15 10:48:48',NULL);
/*!40000 ALTER TABLE `web_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marketing_pages`
--

DROP TABLE IF EXISTS `marketing_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketing_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `eyebrow` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `cta_text` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketing_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marketing_pages`
--

LOCK TABLES `marketing_pages` WRITE;
/*!40000 ALTER TABLE `marketing_pages` DISABLE KEYS */;
INSERT INTO `marketing_pages` VALUES (1,'demo','Demo Interaktif — Coba Veyra Educate & CBT Online','Uji coba langsung antarmuka ujian CBT siswa dari browser Anda dan akses portal demo manajemen sekolah tanpa perlu instalasi rumit.','Simulasi & Demo Sistem Interatif',NULL,'Coba Simulator CBT Siswa','#simulator',1,'2026-11-16 04:00:00','2026-11-16 04:00:00'),(2,'brosur','Brosur Digital — Veyra Educate & CBT Online','Brosur resmi digitalisasi ujian sekolah berbasis komputer (CBT Online) dan portal aplikasi Veyra Educate. Hemat jutaan rupiah biaya cetak kertas, 100% cloud, dan ramah SPJ Dana BOS.','Brosur Resmi',NULL,'Unduh Brosur PDF','#download-brosur',1,'2026-11-16 04:00:00','2026-11-16 04:00:00'),(3,'proposal','Surat Penawaran Resmi — Veyra Educate & CBT Online','Proposal resmi untuk keperluan presentasi kepada dinas pendidikan, kepala sekolah, dan pemangku kepentingan. Format A4 siap cetak dengan branding profesional.','Proposal Resmi',NULL,'Unduh Proposal PDF','#download-proposal',1,'2026-11-16 04:00:00','2026-11-16 04:00:00');
/*!40000 ALTER TABLE `marketing_pages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-16 17:25:07
