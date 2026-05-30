BEGIN;

  ALTER TABLE user_notifications
  ADD reference_2_id INT NULL AFTER reference_id;

COMMIT;