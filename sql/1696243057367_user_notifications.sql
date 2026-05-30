BEGIN;

  ALTER TABLE user_notifications
  RENAME TO notifications;

COMMIT;