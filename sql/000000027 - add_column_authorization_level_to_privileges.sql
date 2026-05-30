BEGIN;

ALTER TABLE privileges
ADD authorization_level INT NULL AFTER name;

COMMIT;