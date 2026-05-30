BEGIN;

  -- CREATE TABLE new_comments (
  --   id INT NOT NULL AUTO_INCREMENT,

  --   deleted_at TIMESTAMP NULL,
  --   updated_at TIMESTAMP NULL,
  --   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  --   PRIMARY KEY (`id`)
  -- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  ALTER TABLE new_comments
  MODIFY type VARCHAR(28) NOT NULL;

COMMIT;