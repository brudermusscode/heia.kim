BEGIN;

  ALTER TABLE feedback
  ADD type TEXT NULL AFTER user_id,
  ADD reference_id INT NULL AFTER user_id;

COMMIT;