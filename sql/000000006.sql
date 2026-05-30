CREATE TABLE user_password_resets(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  token TEXT NULL,
  old_password_encrypted TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;