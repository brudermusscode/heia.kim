BEGIN;

CREATE TABLE clan_modes(
  id INT NOT NULL AUTO_INCREMENT,
  clan_id INT NULL,
  mode TEXT NULL,
  deleted_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE INDEX clan_id
ON clan_modes (clan_id);

COMMIT;
