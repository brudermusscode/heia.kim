CREATE TABLE squad_posts (
  id INT NOT NULL AUTO_INCREMENT,
  squad_id INT NOT NULL,
  user_id INT NOT NULL,
  type VARCHAR(72) NOT NULL,
  comment_string TEXT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX squad_id (squad_id),
  INDEX user_id (user_id)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8mb4_general_ci;
