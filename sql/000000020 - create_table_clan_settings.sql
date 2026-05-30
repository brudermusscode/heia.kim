BEGIN;

CREATE TABLE clan_settings(
  id INT NOT NULL AUTO_INCREMENT,
  clan_id INT NULL,
  image TEXT NULL,
  logo TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX clan_id_index
ON clan_settings (clan_id);

ALTER TABLE clans
  MODIFY created_at TIMESTAMP;

ALTER TABLE clans
  ADD updated_at TIMESTAMP NULL AFTER owner,
  ADD deleted_at TIMESTAMP NULL AFTER owner;

COMMIT;