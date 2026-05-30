BEGIN;

  ALTER TABLE user_privileges
  CHANGE COLUMN privilege_id privilege_name VARCHAR(255) NOT NULL;

COMMIT;