BEGIN;

  ALTER TABLE new_comments
    ADD reference_2_id INT NULL after reference_id;

  CREATE TABLE `clan_comments` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `clan_id` int NOT NULL,
    `type` varchar(256) NOT NULL,
    `reference_id` INT NOT NULL,
    `comment_string` TEXT NOT NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;