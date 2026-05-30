BEGIN;

  -- CREATE TABLE api_discord (
  --   id INT NOT NULL AUTO_INCREMENT,

  --   deleted_at TIMESTAMP NULL,
  --   updated_at TIMESTAMP NULL,
  --   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  --   PRIMARY KEY (`id`)
  -- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

  ALTER TABLE api_discord
  ADD discord_user_id TINYINT DEFAULT 0 AFTER user_id;

COMMIT;