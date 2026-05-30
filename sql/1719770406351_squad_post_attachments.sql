CREATE TABLE squad_post_attachments (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  attachment_id INT NOT NULL,
  type VARCHAR(255) NOT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX user_id (user_id),
  INDEX post_id (post_id)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;