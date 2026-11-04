/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: jne_app
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-0+deb13u1 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `beban_ops`
--

DROP TABLE IF EXISTS `beban_ops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `beban_ops` (
  `id_ops` varchar(10) NOT NULL,
  `beban_ops` varchar(100) NOT NULL,
  `ket_ops` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ops`),
  UNIQUE KEY `beban_ops_beban_ops_unique` (`beban_ops`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beban_ops`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `beban_ops` WRITE;
/*!40000 ALTER TABLE `beban_ops` DISABLE KEYS */;
INSERT INTO `beban_ops` VALUES
('OP-001','Listrik & Air Kantor','Tagihan utilitas PLN & PAM bulanan kantor operasional','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OP-002','Bahan Bakar & Tol Armada','Bensin pertalite/solar dan uang tol kurir pick up harian','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OP-003','Lakban & Perlengkapan Packing','Pembelian lakban cokelat, bubble wrap, plastik wrapping, kardus','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OP-004','Konsumsi & Lembur Karyawan','Biaya makan malam staf sortasi saat overload malam hari','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OP-005','Perawatan Kendaraan & Servis','Ganti oli, servis berkala motor pickup dan mobil van','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `beban_ops` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cabangs`
--

DROP TABLE IF EXISTS `cabangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabangs` (
  `id_cab` varchar(10) NOT NULL,
  `nama_cabang` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `nama_pic` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_cab`),
  UNIQUE KEY `cabangs_nama_cabang_unique` (`nama_cabang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabangs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cabangs` WRITE;
/*!40000 ALTER TABLE `cabangs` DISABLE KEYS */;
INSERT INTO `cabangs` VALUES
('CB-001','Cabang Tomang Raya','Jl. Tomang Raya No. 11, Jakarta Barat','Budi Santoso','081122334455','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('CB-002','Cabang Kebon Jeruk','Jl. Surya Mandala No. 8, Jakarta Barat','Siti Nurhaliza','081233445566','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('CB-003','Cabang Cengkareng Hub','Jl. Daan Mogot Km 14, Jakarta Barat','Hendra Setiawan','081344556677','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('CB-004','Cabang Meruya Selatan','Jl. Meruya Selatan No. 25, Jakarta Barat','Rina Wijayanti','081455667788','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `cabangs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
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

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `komisis`
--

DROP TABLE IF EXISTS `komisis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `komisis` (
  `kode_trx_cl` varchar(20) NOT NULL,
  `id_cab` varchar(10) NOT NULL,
  `tanggal_tf` date NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `ket_trx_cl` text DEFAULT NULL,
  `created_by` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_trx_cl`),
  KEY `komisis_created_by_foreign` (`created_by`),
  KEY `komisis_tanggal_tf_index` (`tanggal_tf`),
  KEY `komisis_id_cab_index` (`id_cab`),
  CONSTRAINT `komisis_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`),
  CONSTRAINT `komisis_id_cab_foreign` FOREIGN KEY (`id_cab`) REFERENCES `cabangs` (`id_cab`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `komisis`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `komisis` WRITE;
/*!40000 ALTER TABLE `komisis` DISABLE KEYS */;
INSERT INTO `komisis` VALUES
('CL-20250131-001','CB-001','2025-01-31',5250000.00,'Pencairan komisi kemitraan JNE periode Januari 2025','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('CL-20250228-002','CB-002','2025-02-28',6100000.00,'Pencairan komisi kemitraan JNE periode Februari 2025','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('CL-20250331-003','CB-001','2025-03-31',4900000.00,'Pencairan komisi kemitraan JNE periode Maret 2025','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `komisis` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0000_01_01_000000_create_statuses_table',1),
(2,'0001_01_01_000000_create_users_table',1),
(3,'0001_01_01_000001_create_cache_table',1),
(4,'0001_01_01_000002_create_jobs_table',1),
(5,'2025_01_01_000001_create_cabangs_table',1),
(6,'2025_01_01_000002_create_beban_ops_table',1),
(7,'2025_01_01_000003_create_packing_lists_table',1),
(8,'2025_01_01_000004_create_operasionals_table',1),
(9,'2025_01_01_000005_create_komisis_table',1),
(10,'2026_11_04_000001_add_id_cab_to_transactions_tables',2),
(11,'2026_11_04_000002_update_admin_counter_to_admin_operasional',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `operasionals`
--

DROP TABLE IF EXISTS `operasionals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `operasionals` (
  `kode_trx_ops` varchar(20) NOT NULL,
  `id_cab` varchar(10) NOT NULL,
  `tanggal_trx_ops` date NOT NULL,
  `beban_ops_id` varchar(10) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `ket_trx_ops` text DEFAULT NULL,
  `created_by` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_trx_ops`),
  KEY `operasionals_beban_ops_id_foreign` (`beban_ops_id`),
  KEY `operasionals_created_by_foreign` (`created_by`),
  KEY `operasionals_tanggal_trx_ops_index` (`tanggal_trx_ops`),
  KEY `operasionals_id_cab_index` (`id_cab`),
  CONSTRAINT `operasionals_beban_ops_id_foreign` FOREIGN KEY (`beban_ops_id`) REFERENCES `beban_ops` (`id_ops`),
  CONSTRAINT `operasionals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`),
  CONSTRAINT `operasionals_id_cab_foreign` FOREIGN KEY (`id_cab`) REFERENCES `cabangs` (`id_cab`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operasionals`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `operasionals` WRITE;
/*!40000 ALTER TABLE `operasionals` DISABLE KEYS */;
INSERT INTO `operasionals` VALUES
('OPS-20250106-001','CB-001','2025-01-06','OP-001',450000.00,'Bayar token PLN kantor cabang Tomang','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OPS-20250114-002','CB-002','2025-01-14','OP-002',320000.00,'Bensin motor kurir minggu ke-2','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OPS-20250125-003','CB-001','2025-01-25','OP-003',275000.00,'Beli 2 dus lakban cokelat + 1 roll bubble wrap','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OPS-20250208-004','CB-002','2025-02-08','OP-005',350000.00,'Ganti oli dan kampas rem motor operasional B 3456 TMG','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OPS-20250220-005','CB-001','2025-02-20','OP-004',180000.00,'Makan malam lembur tim sortir banjir kiriman','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('OPS-20250302-006','CB-002','2025-03-02','OP-001',470000.00,'Tagihan PAM & Listrik awal Maret','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `operasionals` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `packing_lists`
--

DROP TABLE IF EXISTS `packing_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `packing_lists` (
  `kode_trx_pl` varchar(20) NOT NULL,
  `id_cab` varchar(10) NOT NULL,
  `tanggal_pl` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `discount_charge` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL,
  `jumlah_paket` int(10) unsigned NOT NULL,
  `ket_trx_pl` text DEFAULT NULL,
  `created_by` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_trx_pl`),
  KEY `packing_lists_created_by_foreign` (`created_by`),
  KEY `packing_lists_tanggal_pl_index` (`tanggal_pl`),
  KEY `packing_lists_id_cab_index` (`id_cab`),
  CONSTRAINT `packing_lists_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`),
  CONSTRAINT `packing_lists_id_cab_foreign` FOREIGN KEY (`id_cab`) REFERENCES `cabangs` (`id_cab`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packing_lists`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `packing_lists` WRITE;
/*!40000 ALTER TABLE `packing_lists` DISABLE KEYS */;
INSERT INTO `packing_lists` VALUES
('PL-20250105-001','CB-001','2025-01-05',2450000.00,125000.00,2325000.00,142,'Pengiriman barang reguler & YES sesi pagi-sore','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('PL-20250112-002','CB-002','2025-01-12',3100000.00,150000.00,2950000.00,185,'Pengiriman e-commerce weekend promo','USR-002','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('PL-20250120-003','CB-001','2025-01-20',1850000.00,95000.00,1755000.00,110,'Pick up marketplace Tokopedia & Shopee','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('PL-20250203-004','CB-002','2025-02-03',2800000.00,140000.00,2660000.00,165,'Awal bulan lonjakan pengiriman gajian','USR-002','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('PL-20250215-005','CB-001','2025-02-15',3450000.00,175000.00,3275000.00,210,'Event Valentine & Chinese New Year cargo','USR-001','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('PL-20250301-006','CB-002','2025-03-01',2100000.00,100000.00,2000000.00,128,'Pengiriman normal awal bulan Maret','USR-002','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `packing_lists` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `id_status` varchar(10) NOT NULL,
  `nama_status` varchar(50) NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `ket_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_status`),
  UNIQUE KEY `statuses_nama_status_unique` (`nama_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES
('ST-001','Aktif',1,'Status user atau operasional aktif dan diizinkan login','2026-11-03 13:11:53','2026-11-03 13:11:53'),
('ST-002','Cuti',2,'Status karyawan dalam masa cuti, login ditangguhkan','2026-11-03 13:11:53','2026-11-03 13:11:53'),
('ST-003','Suspend',3,'Status dibekukan karena pelanggaran SOP atau investigasi','2026-11-03 13:11:54','2026-11-03 13:11:54'),
('ST-004','Nonaktif',4,'Status akun dinonaktifkan dan tidak memiliki akses ke sistem','2026-11-03 13:11:54','2026-11-03 13:11:54');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` varchar(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `status_id` varchar(10) NOT NULL,
  `role` enum('owner','admin') NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_status_id_foreign` (`status_id`),
  CONSTRAINT `users_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
('USR-001','owner_utama','081234567890','Jl. Tomang Raya No. 12, Jakarta Barat','ST-001','owner','$2y$04$TEu27.tMl4sl.sWv.Ajb0.cudoEDDwDdwK.IVCSLozL/Qg1ETH7dW',NULL,'2026-11-03 13:11:54','2026-11-03 13:32:44'),
('USR-002','admin_operasional','081398765432','Jl. Kebon Jeruk No. 45, Jakarta Barat','ST-001','admin','$2y$04$tcK.Nw9afWJ2/IM15U1AjOkSXZKlTqlPE25LWSkOCDUuyUH3rLt4W',NULL,'2026-11-03 13:11:54','2026-11-03 13:32:44'),
('USR-998','user_cuti_test','081234567899','Jl. Cuti No. 1','ST-002','admin','$2y$04$xauUTTdY5uZX/Haa1sjWQOLxKtjyA.PdGLSDERkQ/IoEA95zxfXuW',NULL,'2026-11-03 13:32:44','2026-11-03 13:32:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-11-04 10:02:33
