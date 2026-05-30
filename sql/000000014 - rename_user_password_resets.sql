BEGIN;

ALTER TABLE user_password_resets
  RENAME TO password_resets;

COMMIT;