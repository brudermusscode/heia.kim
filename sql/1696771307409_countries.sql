BEGIN;

  CREATE TABLE `countries` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `abbreviation` CHAR(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;