DROP TABLE IF EXISTS api_discord;
DROP TABLE IF EXISTS api_google;
DROP TABLE IF EXISTS api_spotify;

CREATE TABLE `connect_credentials` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NULL,
  `type` varchar(12) NULL COLLATE latin1_swedish_ci,
  `vendor_id` varchar(32) NULL COLLATE latin1_swedish_ci,
  `vendor_email` varchar(64) NULL COLLATE utf8mb4_general_ci,
  `access_token` TEXT NULL COLLATE utf8mb4_general_ci,
  `refresh_token` TEXT NULL COLLATE utf8mb4_general_ci,
  `token_type` varchar(64) NULL COLLATE latin1_swedish_ci,
  `token_id` TEXT NULL COLLATE utf8mb4_general_ci,
  `scope` TEXT NULL COLLATE utf8mb4_general_ci,

  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_user_id (`user_id`),
  INDEX idx_vendor_id (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;