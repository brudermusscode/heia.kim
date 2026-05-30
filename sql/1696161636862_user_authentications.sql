BEGIN;

  ALTER TABLE user_authentications
  RENAME TO authentications;

COMMIT;