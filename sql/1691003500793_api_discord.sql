BEGIN;

  -- CREATE TABLE api_discord (
  --   id INT NOT NULL AUTO_INCREMENT,

  --   deleted_at TIMESTAMP NULL,
  --   updated_at TIMESTAMP NULL,
  --   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  --   PRIMARY KEY (`id`)
  -- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  ALTER TABLE api_discord MODIFY COLUMN user_id INT NULL;

COMMIT;