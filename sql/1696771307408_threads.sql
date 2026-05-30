BEGIN;

  CREATE TABLE `threads` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `clan_id` int NOT NULL,
    `user_id` int NOT NULL,
    `title` TEXT NOT NULL,
    `closed` TINYINT(1) NOT NULL DEFAULT 0,
    `pinned` TINYINT(1) NOT NULL DEFAULT 0,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  CREATE TABLE `thread_posts` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `thread_id` int NOT NULL,
    `user_id` int NOT NULL,
    `content` TEXT NOT NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  CREATE TABLE `thread_post_attachments` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `thread_post_id` int NOT NULL,
    `type` VARCHAR(128) NOT NULL,
    `reference_id` int NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;