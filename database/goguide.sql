-- MySQL dump 10.13  Distrib 8.0.18, for Win64 (x86_64)
--
-- Host: localhost    Database: goguide
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area` (
  `id_area` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_area` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kabupaten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'Kintamani','Bangli',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(2,'Ubud','Gianyar',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(3,'Canggu','Badung',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(4,'Uluwatu','Badung',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(5,'Nusa Penida','Klungkung',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(6,'Sanur','Denpasar',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(7,'lovina','buleleng',NULL,NULL,NULL,'2026-11-17 18:27:07','2026-11-17 18:27:07');
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bahan_bakar`
--

DROP TABLE IF EXISTS `bahan_bakar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahan_bakar` (
  `id_bahan_bakar` int unsigned NOT NULL AUTO_INCREMENT,
  `id_booking` int unsigned NOT NULL,
  `jenis_bahan_bakar` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_liter` decimal(6,2) NOT NULL,
  `harga_per_liter` int unsigned NOT NULL,
  `total_biaya` int unsigned NOT NULL,
  `tanggal_isi` date NOT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_bahan_bakar`),
  KEY `bahan_bakar_id_booking_foreign` (`id_booking`),
  CONSTRAINT `bahan_bakar_id_booking_foreign` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bahan_bakar`
--

LOCK TABLES `bahan_bakar` WRITE;
/*!40000 ALTER TABLE `bahan_bakar` DISABLE KEYS */;
INSERT INTO `bahan_bakar` VALUES (1,1,'Pertalite',5.00,10000,50000,'2026-10-25','Antar jemput hotel - Kintamani','2026-10-27 17:56:00','2026-10-27 17:56:00'),(2,2,'Pertalite',4.50,10000,45000,'2026-10-26',NULL,'2026-10-27 17:56:00','2026-10-27 17:56:00'),(3,5,'Pertamax',8.00,12500,100000,'2026-10-21','Antar ke pelabuhan Sanur','2026-10-27 17:56:00','2026-10-27 17:56:00'),(4,6,'Pertamax',20.00,1200,24000,'2026-11-23','yap','2026-11-22 20:05:53','2026-11-22 20:05:53');
/*!40000 ALTER TABLE `bahan_bakar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bahasa`
--

DROP TABLE IF EXISTS `bahasa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bahasa` (
  `id_bahasa` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_bahasa` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_bahasa`),
  UNIQUE KEY `bahasa_nama_bahasa_unique` (`nama_bahasa`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bahasa`
--

LOCK TABLES `bahasa` WRITE;
/*!40000 ALTER TABLE `bahasa` DISABLE KEYS */;
INSERT INTO `bahasa` VALUES (1,'Indonesia','2026-10-27 17:49:54','2026-10-27 17:49:54'),(2,'Inggris','2026-10-27 17:49:54','2026-10-27 17:49:54'),(3,'Jepang','2026-10-27 17:49:54','2026-10-27 17:49:54'),(4,'Mandarin','2026-10-27 17:49:54','2026-10-27 17:49:54'),(5,'Korea','2026-10-27 17:49:54','2026-10-27 17:49:54'),(6,'Prancis','2026-10-27 17:49:54','2026-10-27 17:49:54');
/*!40000 ALTER TABLE `bahasa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking` (
  `id_booking` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_wisatawan` int unsigned NOT NULL,
  `id_paket` int unsigned NOT NULL,
  `id_guide` int unsigned NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_mulai` time DEFAULT '08:00:00',
  `jumlah_peserta` int unsigned NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `harga_satuan` int unsigned NOT NULL,
  `total_bayar` int unsigned NOT NULL,
  `fee_guide` int unsigned NOT NULL DEFAULT '0',
  `kode_va` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_transaksi_gateway` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batas_bayar` datetime DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `alasan_batal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dikonfirmasi_oleh` int unsigned DEFAULT NULL,
  `tanggal_konfirmasi` datetime DEFAULT NULL,
  `status_bayar_guide` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `tanggal_bayar_guide` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_booking`),
  UNIQUE KEY `booking_kode_booking_unique` (`kode_booking`),
  KEY `booking_id_paket_foreign` (`id_paket`),
  KEY `booking_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`),
  KEY `booking_id_guide_tanggal_booking_index` (`id_guide`,`tanggal_booking`),
  KEY `booking_id_customer_foreign` (`id_wisatawan`),
  CONSTRAINT `booking_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `booking_id_guide_foreign` FOREIGN KEY (`id_guide`) REFERENCES `guide` (`id_guide`),
  CONSTRAINT `booking_id_paket_foreign` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`),
  CONSTRAINT `booking_id_wisatawan_foreign` FOREIGN KEY (`id_wisatawan`) REFERENCES `wisatawan` (`id_wisatawan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` VALUES (1,'GGG-DEMO0001',2,2,1,'2026-10-20 01:56:00','2026-10-25','08:00:00',2,'Mohon dijemput di hotel.',350000,700000,490000,'8808123400001',NULL,'SIM-GGG-DEMO0001','2026-10-29 01:56:00','2026-10-22 01:56:00','dikonfirmasi',NULL,3,'2026-10-23 01:56:00','belum',NULL,'2026-10-27 17:56:00','2026-10-27 17:56:00'),(2,'GGG-DEMO0002',2,5,1,'2026-10-21 01:56:00','2026-10-26','08:00:00',3,NULL,300000,900000,630000,'8808123400002',NULL,'SIM-GGG-DEMO0002','2026-10-29 01:56:00','2026-10-23 01:56:00','sudah_bayar',NULL,NULL,NULL,'belum',NULL,'2026-10-27 17:56:00','2026-10-27 17:56:00'),(3,'GGG-DEMO0003',3,1,2,'2026-10-22 01:56:00','2026-10-25','08:00:00',4,NULL,450000,1800000,1260000,'8808123400003',NULL,'SIM-GGG-DEMO0003','2026-10-29 01:56:00',NULL,'dibatalkan','Batas waktu pembayaran terlewati',NULL,NULL,'belum',NULL,'2026-10-27 17:56:00','2026-11-22 19:53:49'),(4,'GGG-DEMO0004',3,3,2,'2026-10-23 01:56:00','2026-10-27','08:00:00',2,NULL,400000,800000,560000,'8808123400004',NULL,'SIM-GGG-DEMO0004','2026-10-29 01:56:00',NULL,'dibatalkan','Batas waktu pembayaran terlewati',NULL,NULL,'belum',NULL,'2026-10-27 17:56:00','2026-10-27 17:56:00'),(5,'GGG-DEMO0005',2,4,1,'2026-10-24 01:56:00','2026-10-21','08:00:00',2,NULL,650000,1300000,910000,'8808123400005',NULL,'SIM-GGG-DEMO0005','2026-10-29 01:56:00','2026-10-26 01:56:00','selesai',NULL,3,'2026-10-27 01:56:00','belum',NULL,'2026-10-27 17:56:00','2026-11-22 19:44:51'),(6,'GGG-6HTWZ3FE',4,8,1,'2026-11-23 03:59:27','2026-11-25','08:00:00',1,'dadadad',1000000,1000000,700000,'8808123400006','Virtual Account (simulasi)','SIM-GGG-6HTWZ3FE','2026-11-24 03:59:27','2026-11-23 03:59:38','dikonfirmasi',NULL,3,'2026-11-23 04:01:06','belum',NULL,'2026-11-22 19:59:27','2026-11-22 20:01:06');
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('goguide-cache-bagas@gmail.com|127.0.0.1','i:2;',1795406268),('goguide-cache-bagas@gmail.com|127.0.0.1:timer','i:1795406268;',1795406268),('goguide-cache-guide1@goguide.test|127.0.0.1','i:1;',1795406236),('goguide-cache-guide1@goguide.test|127.0.0.1:timer','i:1795406236;',1795406236);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `detail_bahasa_guide`
--

DROP TABLE IF EXISTS `detail_bahasa_guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_bahasa_guide` (
  `id_detail_bahasa` int unsigned NOT NULL AUTO_INCREMENT,
  `id_guide` int unsigned NOT NULL,
  `id_bahasa` int unsigned NOT NULL,
  `tingkat_kemampuan` enum('Dasar','Menengah','Mahir','Fasih') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menengah',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_bahasa`),
  UNIQUE KEY `detail_bahasa_guide_id_guide_id_bahasa_unique` (`id_guide`,`id_bahasa`),
  KEY `detail_bahasa_guide_id_bahasa_foreign` (`id_bahasa`),
  CONSTRAINT `detail_bahasa_guide_id_bahasa_foreign` FOREIGN KEY (`id_bahasa`) REFERENCES `bahasa` (`id_bahasa`) ON DELETE CASCADE,
  CONSTRAINT `detail_bahasa_guide_id_guide_foreign` FOREIGN KEY (`id_guide`) REFERENCES `guide` (`id_guide`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_bahasa_guide`
--

LOCK TABLES `detail_bahasa_guide` WRITE;
/*!40000 ALTER TABLE `detail_bahasa_guide` DISABLE KEYS */;
INSERT INTO `detail_bahasa_guide` VALUES (1,1,1,'Fasih',NULL,NULL),(2,1,2,'Mahir',NULL,NULL),(3,2,1,'Fasih',NULL,NULL),(4,2,2,'Menengah',NULL,NULL),(5,2,3,'Dasar',NULL,NULL),(6,4,2,'Menengah','2026-11-21 19:36:27','2026-11-21 19:36:27'),(7,4,3,'Menengah','2026-11-21 19:36:27','2026-11-21 19:36:27'),(8,4,5,'Menengah','2026-11-21 19:36:27','2026-11-21 19:36:27');
/*!40000 ALTER TABLE `detail_bahasa_guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_paket_wisata`
--

DROP TABLE IF EXISTS `detail_paket_wisata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_paket_wisata` (
  `id_detail_paket` int unsigned NOT NULL AUTO_INCREMENT,
  `id_paket` int unsigned NOT NULL,
  `id_objek` int unsigned NOT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '1',
  `durasi_kunjungan` smallint unsigned DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_paket`),
  UNIQUE KEY `detail_paket_wisata_id_paket_id_objek_unique` (`id_paket`,`id_objek`),
  KEY `detail_paket_wisata_id_objek_foreign` (`id_objek`),
  CONSTRAINT `detail_paket_wisata_id_objek_foreign` FOREIGN KEY (`id_objek`) REFERENCES `objek_wisata` (`id_objek`),
  CONSTRAINT `detail_paket_wisata_id_paket_foreign` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_paket_wisata`
--

LOCK TABLES `detail_paket_wisata` WRITE;
/*!40000 ALTER TABLE `detail_paket_wisata` DISABLE KEYS */;
INSERT INTO `detail_paket_wisata` VALUES (1,1,1,1,NULL,NULL,NULL,NULL),(2,1,2,2,NULL,NULL,NULL,NULL),(3,2,3,1,NULL,NULL,NULL,NULL),(4,2,4,2,NULL,NULL,NULL,NULL),(5,3,5,1,NULL,NULL,NULL,NULL),(6,4,6,1,NULL,NULL,NULL,NULL),(7,5,7,1,NULL,NULL,NULL,NULL),(8,5,8,2,NULL,NULL,NULL,NULL),(9,6,5,7,60,NULL,'2026-11-17 05:03:00','2026-11-17 05:03:00'),(10,6,4,8,60,NULL,'2026-11-17 05:03:00','2026-11-17 05:03:00'),(12,7,9,4,60,NULL,'2026-11-17 18:39:58','2026-11-17 18:39:58'),(13,7,6,6,60,NULL,'2026-11-17 18:39:58','2026-11-17 18:39:58'),(14,8,10,1,60,NULL,'2026-11-17 20:28:40','2026-11-17 20:28:40'),(15,8,11,4,60,NULL,'2026-11-17 20:28:40','2026-11-17 20:28:40');
/*!40000 ALTER TABLE `detail_paket_wisata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `guide`
--

DROP TABLE IF EXISTS `guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guide` (
  `id_guide` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned DEFAULT NULL,
  `nama_guide` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_wa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio_singkat` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio_lengkap_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sertifikat_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pengalaman` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `status_verifikasi` enum('Menunggu','Terverifikasi','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `disetujui_oleh` int unsigned DEFAULT NULL,
  `tanggal_persetujuan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_guide`),
  UNIQUE KEY `guide_email_unique` (`email`),
  UNIQUE KEY `guide_id_user_unique` (`id_user`),
  KEY `guide_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `guide_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `guide_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guide`
--

LOCK TABLES `guide` WRITE;
/*!40000 ALTER TABLE `guide` DISABLE KEYS */;
INSERT INTO `guide` VALUES (1,1,'Ayu Lestari','guide1@goguide.test',NULL,NULL,NULL,'Guide lokal berpengalaman di Bali.',NULL,NULL,'Guide wisata alam dan budaya sejak 2019.',NULL,0.00,'Terverifikasi','Aktif',NULL,NULL,'2026-10-27 23:05:11','2026-10-24 03:27:34','2026-10-27 23:05:11'),(2,2,'Made Wirawan','guide2@goguide.test',NULL,NULL,NULL,'Guide lokal berpengalaman di Bali.',NULL,NULL,'Pemandu pendakian dan pura sejak 2017.',NULL,0.00,'Terverifikasi','Aktif',NULL,NULL,'2026-10-27 23:05:11','2026-10-24 03:27:34','2026-10-27 23:05:11'),(3,NULL,'Komang Dewi (calon guide)','calon.guide@goguide.test','081234567890',NULL,NULL,'Pemandu budaya dan pura di Bali.',NULL,NULL,NULL,NULL,0.00,'Menunggu','Aktif',NULL,NULL,NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(4,NULL,'bagas ardiansyah','bagas@gmail.com','08573625763',NULL,'Jalan Kakak Tua D2 No.9 Purigading Jimbaran','yap','guide-bio/Bb1bejN5gNPgARGODkg8ZKkphvPJspsbLNVTCFsh.pdf',NULL,'7 tahun. hai [Bersertifikat]','images/guide/bagas-ardiansyah-1795318587.jpeg',0.00,'Ditolak','Aktif','maaf gak lulus yah',3,'2026-11-21 19:37:43','2026-11-21 19:36:27','2026-11-21 19:37:43');
/*!40000 ALTER TABLE `guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_guide`
--

DROP TABLE IF EXISTS `jadwal_guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_guide` (
  `id_jadwal` int unsigned NOT NULL AUTO_INCREMENT,
  `id_guide` int unsigned NOT NULL,
  `tanggal_aktif` date NOT NULL,
  `jam_mulai` time NOT NULL DEFAULT '08:00:00',
  `jam_selesai` time NOT NULL DEFAULT '17:00:00',
  `status` enum('Tersedia','Tidak Tersedia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tersedia',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  UNIQUE KEY `jadwal_guide_id_guide_tanggal_aktif_unique` (`id_guide`,`tanggal_aktif`),
  CONSTRAINT `jadwal_guide_id_guide_foreign` FOREIGN KEY (`id_guide`) REFERENCES `guide` (`id_guide`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_guide`
--

LOCK TABLES `jadwal_guide` WRITE;
/*!40000 ALTER TABLE `jadwal_guide` DISABLE KEYS */;
INSERT INTO `jadwal_guide` VALUES (1,1,'2026-10-25','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(2,1,'2026-10-26','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(3,1,'2026-10-27','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(4,1,'2026-10-29','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(5,1,'2026-10-30','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(6,1,'2026-10-31','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(7,1,'2026-11-02','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(8,1,'2026-11-03','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(9,1,'2026-11-04','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(10,1,'2026-11-06','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(11,1,'2026-11-07','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(12,1,'2026-11-08','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(13,1,'2026-11-10','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(14,1,'2026-11-11','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(15,1,'2026-11-12','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(16,2,'2026-10-25','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(17,2,'2026-10-26','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(18,2,'2026-10-27','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(19,2,'2026-10-29','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(20,2,'2026-10-30','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(21,2,'2026-10-31','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(22,2,'2026-11-02','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(23,2,'2026-11-03','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(24,2,'2026-11-04','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(25,2,'2026-11-06','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(26,2,'2026-11-07','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(27,2,'2026-11-08','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(28,2,'2026-11-10','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(29,2,'2026-11-11','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(30,2,'2026-11-12','08:00:00','17:00:00','Tersedia',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(31,1,'2026-11-14','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(32,1,'2026-11-15','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(33,1,'2026-11-16','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(34,2,'2026-11-14','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(35,2,'2026-11-15','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(36,2,'2026-11-16','08:00:00','17:00:00','Tersedia',NULL,'2026-10-27 17:49:55','2026-10-27 17:49:55'),(37,1,'2026-11-24','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(38,1,'2026-11-25','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(39,1,'2026-11-26','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(40,1,'2026-11-27','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(41,1,'2026-11-28','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(43,1,'2026-11-30','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(44,1,'2026-12-01','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(45,1,'2026-12-02','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(46,1,'2026-12-03','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(47,1,'2026-12-04','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(48,1,'2026-12-05','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(49,1,'2026-12-06','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(50,1,'2026-12-07','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(51,1,'2026-12-08','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(52,1,'2026-12-09','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(53,1,'2026-12-10','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:40:03','2026-11-22 19:58:16'),(54,1,'2026-11-29','08:00:00','22:00:00','Tersedia',NULL,'2026-11-22 19:58:16','2026-11-22 19:58:16');
/*!40000 ALTER TABLE `jadwal_guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `kategori_wisata`
--

DROP TABLE IF EXISTS `kategori_wisata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_wisata` (
  `id_kategori` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `kategori_wisata_nama_kategori_unique` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_wisata`
--

LOCK TABLES `kategori_wisata` WRITE;
/*!40000 ALTER TABLE `kategori_wisata` DISABLE KEYS */;
INSERT INTO `kategori_wisata` VALUES (1,'Gunung',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(2,'Danau',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(3,'Pantai',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(4,'Air Terjun',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(5,'Budaya',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34'),(6,'Persawahan',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34');
/*!40000 ALTER TABLE `kategori_wisata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_14_130203_create_regions_table',1),(5,'2026_10_14_130204_create_guides_table',1),(6,'2026_10_14_130204_create_tours_table',1),(7,'2026_10_14_130209_add_role_to_users_table',1),(8,'2026_10_18_060640_create_bookings_table',1),(9,'2026_10_18_063916_create_payments_table',1),(10,'2026_10_18_130701_add_phone_and_photo_to_users_table',1),(11,'2026_10_19_000001_create_tour_images_table',1),(12,'2026_10_19_000002_add_profile_fields_to_guides_table',1),(13,'2026_10_24_120000_rancang_ulang_skema_goguide',1),(14,'2026_10_27_100000_selaraskan_skema_dengan_proposal',2),(15,'2026_10_27_110000_tambah_metode_pembayaran_booking',3),(16,'2026_11_17_095945_create_ulasans_table',4),(17,'2026_11_17_110000_rename_customer_to_wisatawan',5),(18,'2026_11_22_120000_tambah_sertifikat_file_ke_guide',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi` (
  `id_notifikasi` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned NOT NULL,
  `id_booking` int unsigned DEFAULT NULL,
  `jenis` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dibaca` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_notifikasi`),
  KEY `notifikasi_id_user_foreign` (`id_user`),
  KEY `notifikasi_id_booking_foreign` (`id_booking`),
  CONSTRAINT `notifikasi_id_booking_foreign` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE,
  CONSTRAINT `notifikasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
INSERT INTO `notifikasi` VALUES (1,3,NULL,'guide_daftar','Pendaftaran guide baru','bagas ardiansyah (bagas@gmail.com) mendaftar sebagai guide dan menunggu verifikasi.',1,'2026-11-21 19:36:27','2026-11-22 19:42:16'),(2,5,5,'selesai','Tour selesai','Tour GGG-DEMO0005 telah selesai. Terima kasih sudah memakai Go Guide.',0,'2026-11-22 19:44:51','2026-11-22 19:44:51'),(3,1,5,'selesai','Tour selesai','Tour GGG-DEMO0005 ditandai selesai oleh admin.',0,'2026-11-22 19:44:51','2026-11-22 19:44:51'),(4,8,6,'booking_baru','Booking dibuat','Booking GGG-6HTWZ3FE dibuat. Bayar Rp1.000.000 ke kode VA 8808123400006 sebelum 24 Nov 2026 03:59.',0,'2026-11-22 19:59:27','2026-11-22 19:59:27'),(5,3,6,'booking_baru','Booking baru belum dibayar','bagas ardiansayah membuat booking GGG-6HTWZ3FE (air dan gunung paket jos jis), menunggu pembayaran.',0,'2026-11-22 19:59:27','2026-11-22 19:59:27'),(6,8,6,'pembayaran_diterima','Pembayaran tercatat','Pembayaran Rp1.000.000 untuk GGG-6HTWZ3FE tercatat. Menunggu verifikasi admin.',0,'2026-11-22 19:59:38','2026-11-22 19:59:38'),(7,3,6,'pembayaran_diterima','Pembayaran perlu diverifikasi','Booking GGG-6HTWZ3FE sudah dibayar (Rp1.000.000). Silakan verifikasi.',0,'2026-11-22 19:59:38','2026-11-22 19:59:38'),(8,8,6,'dikonfirmasi','Booking dikonfirmasi','Booking GGG-6HTWZ3FE dikonfirmasi. Guide: Ayu Lestari (). Tour 25 Nov 2026 pukul 08:00.',0,'2026-11-22 20:01:06','2026-11-22 20:01:06'),(9,1,6,'dikonfirmasi','Tugas tour baru','air dan gunung paket jos jis pada 25 Nov 2026 pukul 08:00, 1 peserta (bagas ardiansayah).',0,'2026-11-22 20:01:06','2026-11-22 20:01:06');
/*!40000 ALTER TABLE `notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `objek_wisata`
--

DROP TABLE IF EXISTS `objek_wisata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `objek_wisata` (
  `id_objek` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_objek` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kategori` int unsigned NOT NULL,
  `id_area` int unsigned NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_tiket` decimal(12,2) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_objek`),
  KEY `objek_wisata_id_kategori_foreign` (`id_kategori`),
  KEY `objek_wisata_id_area_foreign` (`id_area`),
  CONSTRAINT `objek_wisata_id_area_foreign` FOREIGN KEY (`id_area`) REFERENCES `area` (`id_area`),
  CONSTRAINT `objek_wisata_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_wisata` (`id_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `objek_wisata`
--

LOCK TABLES `objek_wisata` WRITE;
/*!40000 ALTER TABLE `objek_wisata` DISABLE KEYS */;
INSERT INTO `objek_wisata` VALUES (1,'Gunung Batur',1,1,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(2,'Danau Batur',2,1,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(3,'Air Terjun Tegenungan',4,2,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(4,'Tegalalang Rice Terrace',6,2,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(5,'Pura Uluwatu',5,4,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(6,'Pantai Kelingking',3,5,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(7,'Pantai Sanur',3,6,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(8,'Pantai Batu Bolong',3,3,NULL,NULL,NULL,NULL,'Aktif','2026-10-24 03:27:34','2026-10-24 03:27:34'),(9,'lovina beach',3,7,'tes','yes','images/objek/lovina-beach-1794969148.jpg',200000.00,'Aktif','2026-11-17 18:32:28','2026-11-17 18:37:05'),(10,'air terjun',4,1,'seger cuy','yap','images/objek/air-terjun-1794975938.jpg',43000.00,'Aktif','2026-11-17 20:25:38','2026-11-17 20:25:38'),(11,'gunung agung',1,4,'dingin hihih','yap','images/objek/gunung-agung-1794976031.png',202000.00,'Aktif','2026-11-17 20:27:11','2026-11-17 20:27:11');
/*!40000 ALTER TABLE `objek_wisata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_wisata`
--

DROP TABLE IF EXISTS `paket_wisata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paket_wisata` (
  `id_paket` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_paket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_paket` int unsigned NOT NULL,
  `persen_fee_guide` tinyint unsigned NOT NULL DEFAULT '0',
  `durasi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `fasilitas` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_paket`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_wisata`
--

LOCK TABLES `paket_wisata` WRITE;
/*!40000 ALTER TABLE `paket_wisata` DISABLE KEYS */;
INSERT INTO `paket_wisata` VALUES (1,'Sunrise Trekking Gunung Batur',450000,70,'8 jam','Paket Sunrise Trekking Gunung Batur bersama guide lokal.','Guide lokal, senter, air mineral, sarapan',NULL,'Aktif','2026-10-24 03:27:34','2026-10-27 17:49:54'),(2,'Ubud Alam dan Air Terjun',350000,70,'6 jam','Paket Ubud Alam dan Air Terjun bersama guide lokal.','Guide lokal, air mineral',NULL,'Aktif','2026-10-24 03:27:34','2026-10-27 17:49:54'),(3,'Uluwatu Pura dan Pantai',400000,70,'5 jam','Paket Uluwatu Pura dan Pantai bersama guide lokal.','Guide lokal, air mineral',NULL,'Aktif','2026-10-24 03:27:34','2026-11-17 04:35:35'),(4,'Nusa Penida Day Trip',650000,70,'10 jam','Paket Nusa Penida Day Trip bersama guide lokal.','Guide lokal, air mineral',NULL,'Aktif','2026-10-24 03:27:34','2026-11-17 04:53:07'),(5,'Sanur dan Canggu Pantai',300000,70,'5 jam','Paket Sanur dan Canggu Pantai bersama guide lokal.','Guide lokal, air mineral',NULL,'Aktif','2026-10-24 03:27:34','2026-10-27 17:49:54'),(6,'tes',200000,70,'8','tes','hey','images/paket/tes-1794920519.jpg','Aktif','2026-11-17 05:01:59','2026-11-17 05:01:59'),(7,'tes1',1111,70,'11','11','1111','images/paket/tes1-1794969598.jpg','Aktif','2026-11-17 18:37:31','2026-11-22 19:45:47'),(8,'air dan gunung paket jos jis',1000000,70,'10 jam','hai','oke sih','images/paket/air-dan-gunung-paket-jos-jis-1794976120.jpeg','Aktif','2026-11-17 20:28:40','2026-11-17 20:28:40');
/*!40000 ALTER TABLE `paket_wisata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_guide`
--

DROP TABLE IF EXISTS `tour_guide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tour_guide` (
  `id_tourguide` int unsigned NOT NULL AUTO_INCREMENT,
  `id_guide` int unsigned NOT NULL,
  `id_paket` int unsigned NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tourguide`),
  UNIQUE KEY `tour_guide_id_guide_id_paket_unique` (`id_guide`,`id_paket`),
  KEY `tour_guide_id_paket_foreign` (`id_paket`),
  CONSTRAINT `tour_guide_id_guide_foreign` FOREIGN KEY (`id_guide`) REFERENCES `guide` (`id_guide`) ON DELETE CASCADE,
  CONSTRAINT `tour_guide_id_paket_foreign` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_guide`
--

LOCK TABLES `tour_guide` WRITE;
/*!40000 ALTER TABLE `tour_guide` DISABLE KEYS */;
INSERT INTO `tour_guide` VALUES (1,1,2,'Aktif',NULL,NULL,NULL),(2,1,5,'Aktif',NULL,NULL,NULL),(3,1,4,'Aktif',NULL,NULL,NULL),(4,2,1,'Aktif',NULL,NULL,NULL),(5,2,3,'Aktif',NULL,NULL,NULL),(6,1,1,'Aktif',NULL,'2026-11-22 19:39:30','2026-11-22 19:39:30'),(7,1,3,'Aktif',NULL,'2026-11-22 19:39:30','2026-11-22 19:39:30'),(8,1,6,'Aktif',NULL,'2026-11-22 19:39:30','2026-11-22 19:39:30'),(9,1,7,'Aktif',NULL,'2026-11-22 19:39:30','2026-11-22 19:39:30'),(10,1,8,'Aktif',NULL,'2026-11-22 19:39:30','2026-11-22 19:39:30');
/*!40000 ALTER TABLE `tour_guide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ulasan`
--

DROP TABLE IF EXISTS `ulasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ulasan` (
  `id_ulasan` int unsigned NOT NULL AUTO_INCREMENT,
  `id_booking` int unsigned NOT NULL,
  `id_wisatawan` int unsigned NOT NULL,
  `id_guide` int unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ulasan`),
  UNIQUE KEY `ulasan_id_booking_unique` (`id_booking`),
  KEY `ulasan_id_guide_foreign` (`id_guide`),
  KEY `ulasan_id_wisatawan_foreign` (`id_wisatawan`),
  CONSTRAINT `ulasan_id_booking_foreign` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE,
  CONSTRAINT `ulasan_id_guide_foreign` FOREIGN KEY (`id_guide`) REFERENCES `guide` (`id_guide`),
  CONSTRAINT `ulasan_id_wisatawan_foreign` FOREIGN KEY (`id_wisatawan`) REFERENCES `wisatawan` (`id_wisatawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ulasan`
--

LOCK TABLES `ulasan` WRITE;
/*!40000 ALTER TABLE `ulasan` DISABLE KEYS */;
/*!40000 ALTER TABLE `ulasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wisatawan',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wajib_ganti_password` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ayu Lestari','ayu@goguide.com',NULL,'images/avatars/1-v4MxRHUaoL.png','guide',NULL,'$2y$12$ywQ2gSEKUoKx9iZwnammfe8ifyYp/jZrvHYoOyFwipcUwZ1rXoTli',NULL,'2026-10-24 03:27:34','2026-10-31 02:45:38','ayu.lestari',0),(2,'Made Wirawan','guide2@goguide.test',NULL,NULL,'guide',NULL,'$2y$12$.RuUUd3hqj3L0lshJstFc.lJHGP0MCE2SGeLZNzHxbCmm83sO78JO',NULL,'2026-10-24 03:27:34','2026-10-24 03:27:34','made.wirawan',0),(3,'Admin','renzou@gmail.com',NULL,NULL,'admin',NULL,'$2y$12$5pM2ZIVdUeFeB3YvzcwLtO6KNxcVR6Nf/e5kV74R47IHmdLhelvLi',NULL,'2026-10-24 03:28:25','2026-10-24 03:28:25',NULL,0),(4,'rara','cicak@gmail.com',NULL,NULL,'wisatawan',NULL,'$2y$12$7yHj1CcX4FNtJySg/X1exOheC8.53NsLo/J4UatcIXC2DwRBoHLRG',NULL,'2026-10-26 04:52:27','2026-10-26 04:52:27',NULL,0),(5,'Budi Santoso','customer1@goguide.test','081234567801',NULL,'wisatawan',NULL,'$2y$12$wwYDkUKRMC12elNqSzKxWu05TYC5eWKmaAToiFhuRtt9btk3Jw3pS',NULL,'2026-10-27 17:49:57','2026-10-27 17:49:57',NULL,0),(6,'Sari Wulandari','customer2@goguide.test','081234567802',NULL,'wisatawan',NULL,'$2y$12$Cai8iWdZ5uac7zUfmZHNyuvEOy56DU75bGRie.72720h6Hup72jEG',NULL,'2026-10-27 17:49:57','2026-10-27 17:49:57',NULL,0),(7,'rizu','rizu@gmail.com',NULL,NULL,'wisatawan',NULL,'$2y$12$O.jvnQZSHJonew53J.5ikuzbVPFLJ.FW1naozQ1hopDF/doczTR6m',NULL,'2026-10-27 17:59:56','2026-10-27 17:59:56',NULL,0),(8,'bagas ardiansayah','luckybagas0407@gmail.com','0858476358',NULL,'wisatawan',NULL,'$2y$12$xDNo6RflmFWGruE1p97MV.y3nLC4VgyPee78jFqXlIiITwZJ7KM5S',NULL,'2026-10-27 22:34:41','2026-11-22 19:59:27',NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wisatawan`
--

DROP TABLE IF EXISTS `wisatawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wisatawan` (
  `id_wisatawan` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned NOT NULL,
  `nama_wisatawan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `status` enum('Aktif','Nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_wisatawan`),
  UNIQUE KEY `customer_id_user_unique` (`id_user`),
  UNIQUE KEY `customer_email_unique` (`email`),
  CONSTRAINT `customer_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wisatawan`
--

LOCK TABLES `wisatawan` WRITE;
/*!40000 ALTER TABLE `wisatawan` DISABLE KEYS */;
INSERT INTO `wisatawan` VALUES (1,4,'rara','cicak@gmail.com',NULL,'2026-10-26 12:52:27','Aktif','2026-10-28 01:49:47','2026-10-28 01:49:47'),(2,5,'Budi Santoso','customer1@goguide.test','081234567801','2026-10-28 01:54:32','Aktif','2026-10-27 17:54:32','2026-10-27 17:54:32'),(3,6,'Sari Wulandari','customer2@goguide.test','081234567802','2026-10-28 01:54:32','Aktif','2026-10-27 17:54:32','2026-10-27 17:54:32'),(4,8,'bagas ardiansayah','luckybagas0407@gmail.com','0858476358','2026-11-23 03:53:49','Aktif','2026-11-22 19:53:49','2026-11-22 19:59:27');
/*!40000 ALTER TABLE `wisatawan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'goguide'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-24 14:02:05
