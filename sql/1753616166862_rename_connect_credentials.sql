-- CREATE TABLE authentications (
--   id INT NOT NULL AUTO_INCREMENT,

--   deleted_at TIMESTAMP NULL,
--   updated_at TIMESTAMP NULL,
--   created_at TIMESTAMP NULL default CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

RENAME TABLE connect_credentials TO connections;

ALTER TABLE connections
  DROP COLUMN token_type,
  RENAME COLUMN type TO provider,
  RENAME COLUMN vendor_id TO provider_user_id,
  RENAME COLUMN vendor_email TO provider_user_email,
  ADD COLUMN expires_at TIMESTAMP NULL after scope;
