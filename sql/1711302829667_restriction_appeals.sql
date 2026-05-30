
  CREATE TABLE `restriction_appeals` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `restriction_id` int NULL,
    `content` TEXT NULL,
    `live_play_file` VARCHAR(128) NULL,
    `is_appeal` TINYINT NULL,

    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (`user_id`),
    INDEX idx_restriction_id (`restriction_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  DROP TABLE relationship_requests;
