BEGIN;

CREATE TABLE clan_feed(
  id INT NOT NULL AUTO_INCREMENT,
  clan_id INT NULL,
  user_id INT NULL,
  type VARCHAR(226) NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE INDEX clan_id
ON clan_feed (clan_id);

CREATE INDEX user_id
ON clan_feed (user_id);

COMMIT;
