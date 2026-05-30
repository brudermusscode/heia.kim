BEGIN;

CREATE TABLE user_authentications(
  id INT NOT NULL AUTO_INCREMENT,
  email TEXT NULL,
  token TEXT NULL,
  serial TEXT NULL,
  secret TEXT NULL,
  remote_address VARCHAR(48) NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE UNIQUE INDEX email
ON user_authentications (email);

COMMIT;
