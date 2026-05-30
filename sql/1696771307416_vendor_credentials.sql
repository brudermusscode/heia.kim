ALTER TABLE connect_credentials
  ADD is_legit TINYINT(1) NULL AFTER type;

ALTER TABLE users
  MODIFY email VARCHAR(126) NULL,
  ADD UNIQUE INDEX unq_email (email);