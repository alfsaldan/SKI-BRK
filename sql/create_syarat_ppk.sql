-- Create table for Syarat PPK
CREATE TABLE IF NOT EXISTS `syarat_ppk` (
  `id_ppk` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `syarat` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_ppk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
