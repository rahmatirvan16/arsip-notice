-- Backup of database: arsip_digital
-- Generated on: 2025-10-21 07:06:34

DROP TABLE IF EXISTS dokumen;
CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(255) NOT NULL,
  `bulan_tahun` varchar(7) NOT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO dokumen VALUES("1","Lunas PKB Agustus 2025","2025-08","Laporan Penggunaan Loket Agustus 2025.pdf","active");
INSERT INTO dokumen VALUES("2","Lunas PKB September 2025","2025-09","Laporan Penggunaan Loket September 2025.pdf","active");
INSERT INTO dokumen VALUES("3","Laporan Penggunaan Notice Onilne 1-15 Agustus 2025","2025-08","Laporan Penggunaan Notice Online 1-15Agustus 2025.pdf","active");
INSERT INTO dokumen VALUES("4","Laporan Penggunaan Notice Online 16-29 Agustus 2025","2025-08","Laporan Penggunaan Notice Online 16-29 Agustus 2025.pdf","active");
INSERT INTO dokumen VALUES("5","Laporan Penggunaan Loket Agustus 2025","2025-08","Laporan Penggunaan Loket Agustus 2025.pdf","active");
INSERT INTO dokumen VALUES("6","Laporan Notice Rusak Agustus 2025","2025-08","Laporan Notice Rusak Agustus 2025.pdf","active");
INSERT INTO dokumen VALUES("7","Laporan Penggunaan Loket September 2025","2025-09","Laporan Penggunaan Loket September 2025.pdf","active");
INSERT INTO dokumen VALUES("8","Laporan Notice Rusak September 2025","2025-09","Laporan Penggunaan Notice Rusak September 2025.pdf","active");
INSERT INTO dokumen VALUES("9","Laporan Penggunaan Notice Online 1-10 September 2025","2025-09","Laporan Penggunaan Notice Online 1-10 Sep 2025.pdf","active");
INSERT INTO dokumen VALUES("10","Laporan Penggunaan Notice Online 11-20 September 2025","2025-09","Laporan Penggunaan Notice Online 11-20 September 2025.pdf","active");
INSERT INTO dokumen VALUES("11","Laporan Penggunaan Notice Online 21-30 September 2025","2025-09","Laporan Penggunaan Notice Online 21-30 Sep 2025.pdf","active");
INSERT INTO dokumen VALUES("12","Laporan Penggunaan Notice Loket Juli 2025","2025-07","Penggunaan Notice Juli 2025.pdf","active");
INSERT INTO dokumen VALUES("13","Laporan Notice Luna PKB Juli 2025","2025-07","Lunas PKB JULI.pdf","active");
INSERT INTO dokumen VALUES("14","Laporan Notice Rusak Juli 2025","2025-07","Notice Rusak Juli 2025.pdf","active");
INSERT INTO dokumen VALUES("15","Laporan Pembayaran Online 1-7 Juli 2025","2025-07","Pembayaran Online 1-7 Juli 2025.pdf","active");
INSERT INTO dokumen VALUES("16","Laporan Pembayaran Online 8-14 Juli 2025","2025-07","Pembayaran Online 8-14 Juli 2025.pdf","active");
INSERT INTO dokumen VALUES("17","Laporan Pembayaran Online 15-21 Juli 2025","2025-07","Laporan Online 15-21 Juli 2025.pdf","active");
INSERT INTO dokumen VALUES("18","Laporan Pembayaran Online 21-31 Juli 2025","2025-07","Laporan Online 21-31 Juli 2025.pdf","active");


DROP TABLE IF EXISTS logs;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `notice_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `dokumen_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO logs VALUES("1","1","restore","1","Restored notice to active status","2025-10-13 14:26:47","");
INSERT INTO logs VALUES("2","1","delete","1","Set notice to inactive status","2025-10-13 15:04:48","");
INSERT INTO logs VALUES("3","1","restore","1","Restored notice to active status","2025-10-13 15:04:51","");
INSERT INTO logs VALUES("4","1","delete","1","Set notice to inactive status","2025-10-13 15:05:19","");
INSERT INTO logs VALUES("5","1","restore","1","Restored notice to active status","2025-10-13 15:06:04","");
INSERT INTO logs VALUES("6","1","edit","1","Edited notice: cd-201","2025-10-13 15:15:07","");
INSERT INTO logs VALUES("7","1","edit","1","Edited notice: cd-201","2025-10-13 15:15:12","");
INSERT INTO logs VALUES("8","1","delete","1","Set notice to inactive status","2025-10-14 10:26:29","");
INSERT INTO logs VALUES("9","1","restore","1","Restored notice to active status","2025-10-14 10:26:34","");
INSERT INTO logs VALUES("10","1","delete","1","Set notice to inactive status","2025-10-14 10:27:26","");
INSERT INTO logs VALUES("11","1","restore","1","Restored notice to active status","2025-10-15 10:17:54","");
INSERT INTO logs VALUES("12","1","edit","1","Edited notice: CD025-0000001","2025-10-21 10:51:51","");
INSERT INTO logs VALUES("13","1","edit","1","Edited notice: CD025-0000001","2025-10-21 10:52:29","");
INSERT INTO logs VALUES("14","1","edit","","Edited dokumen: Laporan Pembayaran Online 21-31 Juli 2025","2025-10-21 11:57:47","18");


DROP TABLE IF EXISTS notices;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_notice` varchar(255) NOT NULL,
  `tanggal_penetapan` date NOT NULL,
  `tanggal_cetak` date NOT NULL,
  `keterangan_rusak` text DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO notices VALUES("1","CD025-0000001","2025-10-14","2025-10-13","Batal","Laporan Online 15-21 Juli 2025.pdf","active");


DROP TABLE IF EXISTS settings;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) NOT NULL DEFAULT 'Sistem Informasi Arsip Notice Digital Aceh',
  `logo_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES("1","Sistem Informasi Arsip Notice Digital Aceh (SIANDA)","logo_samsat-removebg-preview.png");


DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL DEFAULT 'operator',
  `status` varchar(10) DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("1","admin","$2y$10$eC8PhVRMDyePFlqpJfDQAOYKwOJn7ZKM6G5ELMOroLHovnkr57d4K","admin","active");
INSERT INTO users VALUES("2","operator","$2y$10$4lOm0NnRHTa.RnC5oVbBO.ipTjJbxd2ACZeTsKKl6ClqpfTglP8Nu","operator","active");
INSERT INTO users VALUES("5","irvan","$2y$10$wezTt8zSLX2B3kD8FHaqBeRCt85E3ZcFaxaT7FftOI4kNnvLs/kfW","operator","active");


