-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for phpdb
CREATE DATABASE IF NOT EXISTS `phpdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `phpdb`;

-- Dumping structure for table phpdb.nilai
CREATE TABLE IF NOT EXISTS `nilai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nim` varchar(15) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `nilai_huruf` char(1) NOT NULL,
  `predikat` varchar(30) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nim` (`nim`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table phpdb.nilai: ~40 rows (approximately)
INSERT INTO `nilai` (`id`, `nim`, `nama`, `file_path`, `nilai_akhir`, `nilai_huruf`, `predikat`, `created_at`, `updated_at`) VALUES
	(1, '3202316001', 'Ferry Faisal', 'uploads/3202316001_b529f632.jpg', 45.65, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:49:51'),
	(2, '3202316002', 'Ferry', 'assets/img/user3-128x128.jpg', 78.50, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(3, '3202316003', 'Habil', 'assets/img/user4-128x128.jpg', 60.75, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(4, '3202316004', 'Inka', 'assets/img/user5-128x128.jpg', 87.90, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(5, '3202316005', 'Aqil', 'assets/img/user6-128x128.jpg', 77.75, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(6, '3202316006', 'Freky', 'assets/img/user7-128x128.jpg', 79.82, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(7, '3202316007', 'Fadhil', 'assets/img/user8-128x128.jpg', 45.65, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(8, '3202316008', 'Surya', 'assets/img/user1-128x128.jpg', 46.73, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(9, '3202316009', 'Caesha', 'assets/img/user2-160x160.jpg', 89.73, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(10, '3202316010', 'Dewi Lestari', 'assets/img/user3-128x128.jpg', 91.20, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(11, '3202316011', 'Budi Santoso', 'assets/img/user4-128x128.jpg', 68.40, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(12, '3202316012', 'Siti Aisyah', 'assets/img/user5-128x128.jpg', 82.15, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(13, '3202316013', 'Rizky Pratama', 'assets/img/user6-128x128.jpg', 35.90, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(14, '3202316014', 'Nadia Putri', 'assets/img/user7-128x128.jpg', 74.60, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(15, '3202316015', 'Andika Saputra', 'assets/img/user8-128x128.jpg', 56.25, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(16, '3202316016', 'Maya Sari', 'assets/img/user1-128x128.jpg', 88.00, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(17, '3202316017', 'Hendra Wijaya', 'assets/img/user2-160x160.jpg', 52.80, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(18, '3202316018', 'Putri Ananda', 'assets/img/user3-128x128.jpg', 79.10, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(19, '3202316019', 'Dimas Prakoso', 'assets/img/user4-128x128.jpg', 63.45, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(20, '3202316020', 'Lina Marlina', 'assets/img/user5-128x128.jpg', 93.50, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(21, '3202316021', 'Yoga Permana', 'assets/img/user6-128x128.jpg', 41.30, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(22, '3202316022', 'Rina Oktaviani', 'assets/img/user7-128x128.jpg', 72.40, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(23, '3202316023', 'Fajar Nugroho', 'assets/img/user8-128x128.jpg', 58.90, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(24, '3202316024', 'Salsabila Putri', 'assets/img/user1-128x128.jpg', 94.25, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(25, '3202316025', 'Galih Pratama', 'assets/img/user2-160x160.jpg', 33.10, 'E', 'Tidak Lulus', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(26, '3202316026', 'Anisa Rahma', 'assets/img/user3-128x128.jpg', 66.00, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(27, '3202316027', 'Bagas Setiawan', 'assets/img/user4-128x128.jpg', 81.30, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(28, '3202316028', 'Tiara Maharani', 'assets/img/user5-128x128.jpg', 49.75, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(29, '3202316029', 'Reza Maulana', 'assets/img/user6-128x128.jpg', 77.20, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(30, '3202316030', 'Citra Dewanti', 'assets/img/user7-128x128.jpg', 62.80, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(31, '3202316031', 'Arif Hidayat', 'assets/img/user8-128x128.jpg', 28.50, 'E', 'Tidak Lulus', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(32, '3202316032', 'Melati Kusuma', 'assets/img/user1-128x128.jpg', 90.00, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(33, '3202316033', 'Doni Firmansyah', 'assets/img/user2-160x160.jpg', 54.35, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(34, '3202316034', 'Intan Permata', 'assets/img/user3-128x128.jpg', 70.65, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(35, '3202316035', 'Kevin Alexander', 'assets/img/user4-128x128.jpg', 38.40, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(36, '3202316036', 'Wulan Sari', 'assets/img/user5-128x128.jpg', 85.90, 'A', 'Sangat Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(37, '3202316037', 'Hasan Basri', 'assets/img/user6-128x128.jpg', 64.20, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(38, '3202316038', 'Nurul Hidayah', 'assets/img/user7-128x128.jpg', 47.15, 'D', 'Kurang Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(39, '3202316039', 'Rafi Ramadhan', 'assets/img/user8-128x128.jpg', 80.50, 'B', 'Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21'),
	(40, '3202316040', 'Sinta Dewi', 'assets/img/user1-128x128.jpg', 65.50, 'C', 'Cukup Memuaskan', '2026-10-27 02:40:21', '2026-10-27 02:40:21');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
