-- Migration: convert normalized ppk_responses (one row per answer) to JSON-per-nik storage.
-- IMPORTANT: Backup your database before running this script.
-- This script assumes your current `ppk_responses` table may have the older shape:
--   id, nik, id_ppk, answer, created_at, updated_at
-- and will create a new table, aggregate answers per nik into a JSON blob, and swap tables.

START TRANSACTION;

-- 1) Create new table
CREATE TABLE IF NOT EXISTS `ppk_responses_new` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(50) NOT NULL,
  `answers` TEXT NULL COMMENT 'JSON object mapping id_ppk -> answer',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidx_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Aggregate existing normalized rows into JSON per nik.
-- If your MySQL supports JSON_OBJECTAGG (MySQL 5.7+), you can use it. Otherwise we build JSON via GROUP_CONCAT.

-- Using GROUP_CONCAT to build simple JSON. This assumes `answer` values are simple (ya/tidak) and safe.
INSERT INTO `ppk_responses_new` (nik, answers, created_at, updated_at)
SELECT
  nik,
  CONCAT('{', GROUP_CONCAT(CONCAT('"', id_ppk, '"', ':', '"', answer, '"') SEPARATOR ','), '}') AS answers,
  NOW(),
  NOW()
FROM `ppk_responses`
GROUP BY nik;

-- 3) Verify counts (client should check these results before swapping):
-- SELECT (SELECT COUNT(DISTINCT nik) FROM ppk_responses) AS old_unique_nik, (SELECT COUNT(*) FROM ppk_responses_new) AS new_rows;

-- 4) Swap tables (make sure step 3 looks good). This renames the old table to a backup and replaces it.
RENAME TABLE `ppk_responses` TO `ppk_responses_old`, `ppk_responses_new` TO `ppk_responses`;

COMMIT;

-- After verifying the application works against the new table, you may drop the old table:
-- DROP TABLE IF EXISTS `ppk_responses_old`;

-- End migration
