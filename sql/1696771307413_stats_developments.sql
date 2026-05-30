  CREATE TABLE `stat_developments` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `mode` int NOT NULL,
    `rank` int NOT NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;