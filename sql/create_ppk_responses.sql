-- Create table for per-pegawai PPK responses (JSON-per-nik storage)
-- This table stores one row per `nik` with an `answers` JSON/TEXT column.
CREATE TABLE IF NOT EXISTS `ppk_responses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(50) NOT NULL,
  `answers` TEXT NULL COMMENT 'JSON object mapping id_ppk -> answer ("ya"/"tidak")',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
