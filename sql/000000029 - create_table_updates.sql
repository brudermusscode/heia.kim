BEGIN;

CREATE TABLE updates(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  message TEXT NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE UNIQUE INDEX user_id
ON updates (user_id);

COMMIT;
