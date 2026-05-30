BEGIN;

CREATE TABLE user_images(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  image_path TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX user_id_index
ON user_images (user_id);

COMMIT;