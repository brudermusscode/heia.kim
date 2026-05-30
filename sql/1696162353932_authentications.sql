BEGIN;

  ALTER TABLE authentications
  DROP COLUMN serial,
  DROP COLUMN secret,
  ADD deleted_at TIMESTAMP NULL after  remote_address;

COMMIT;