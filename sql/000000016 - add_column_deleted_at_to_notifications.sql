BEGIN;

ALTER TABLE user_notifications
ADD deleted_at TEXT NULL AFTER read_at;

COMMIT;