BEGIN;

CREATE TABLE clan_requests(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  clan_id INT NULL,
  type varchar(28) NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX user_id_index
ON clan_requests (user_id);

CREATE INDEX clan_id_index
ON clan_requests (clan_id);

COMMIT;