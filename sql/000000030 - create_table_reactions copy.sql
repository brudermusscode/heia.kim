BEGIN;

CREATE TABLE reactions(
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  type TEXT NULL,
  reference_id INT NULL,
  reaction VARCHAR(222) NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE INDEX user_id
ON reactions (user_id);

CREATE INDEX reference_id
ON reactions (reference_id);

COMMIT;
